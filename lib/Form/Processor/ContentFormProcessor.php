<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Form\Processor;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\URLAliasService;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\ContentStruct;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentException;
use EzSystems\RepositoryForms\Data\Content\ContentCreateData;
use EzSystems\RepositoryForms\Data\Content\ContentUpdateData;
use EzSystems\RepositoryForms\Data\NewnessCheckable;
use EzSystems\RepositoryForms\Event\FormActionEvent;
use EzSystems\RepositoryForms\Event\RepositoryFormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

/**
 * Listens for and processes RepositoryForm events: publish, remove draft, save draft...
 */
class ContentFormProcessor implements EventSubscriberInterface
{
    /** @var \eZ\Publish\API\Repository\ContentService */
    private $contentService;

    /** @var \eZ\Publish\API\Repository\LocationService */
    private $locationService;

    /** @var \Symfony\Component\Routing\RouterInterface */
    private $router;

    /** @var \eZ\Publish\API\Repository\URLAliasService */
    private $urlAliasService;

    /**
     * @param \eZ\Publish\API\Repository\ContentService $contentService
     * @param \eZ\Publish\API\Repository\LocationService $locationService
     * @param \Symfony\Component\Routing\RouterInterface $router
     * @param \eZ\Publish\API\Repository\URLAliasService $urlAliasService
     */
    public function __construct(
        ContentService $contentService,
        LocationService $locationService,
        RouterInterface $router,
        URLAliasService $urlAliasService
    ) {
        $this->contentService = $contentService;
        $this->locationService = $locationService;
        $this->router = $router;
        $this->urlAliasService = $urlAliasService;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            RepositoryFormEvents::CONTENT_PUBLISH => ['processPublish', 10],
            RepositoryFormEvents::CONTENT_CANCEL => ['processCancel', 10],
            RepositoryFormEvents::CONTENT_SAVE_DRAFT => ['processSaveDraft', 10],
            RepositoryFormEvents::CONTENT_CREATE_DRAFT => ['processCreateDraft', 10],
        ];
    }

    /**
     * @param \EzSystems\RepositoryForms\Event\FormActionEvent $event
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     * @throws \eZ\Publish\API\Repository\Exceptions\ContentFieldValidationException
     * @throws \eZ\Publish\API\Repository\Exceptions\ContentValidationException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentException
     */
    public function processSaveDraft(FormActionEvent $event)
    {
        /** @var \EzSystems\RepositoryForms\Data\Content\ContentCreateData|\EzSystems\RepositoryForms\Data\Content\ContentUpdateData $data */
        $data = $event->getData();
        $form = $event->getForm();

        $formConfig = $form->getConfig();
        $languageCode = $formConfig->getOption('languageCode');
        $draft = $this->saveDraft($data, $languageCode);
        $referrerLocation = $event->getOption('referrerLocation');
        $contentLocation = $this->resolveLocation($draft, $referrerLocation, $data);

        $defaultUrl = $this->router->generate('ez_content_draft_edit', [
            'contentId' => $draft->id,
            'versionNo' => $draft->getVersionInfo()->versionNo,
            'language' => $languageCode,
            'locationId' => null !== $contentLocation ? $contentLocation->id : null,
        ]);
        $event->setResponse(new RedirectResponse($formConfig->getAction() ?: $defaultUrl));
    }

    /**
     * @param \EzSystems\RepositoryForms\Event\FormActionEvent $event
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     * @throws \eZ\Publish\API\Repository\Exceptions\ContentFieldValidationException
     * @throws \eZ\Publish\API\Repository\Exceptions\ContentValidationException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentException
     */
    public function processPublish(FormActionEvent $event)
    {
        /** @var \EzSystems\RepositoryForms\Data\Content\ContentCreateData|\EzSystems\RepositoryForms\Data\Content\ContentUpdateData $data */
        $data = $event->getData();
        $form = $event->getForm();

        $draft = $this->saveDraft($data, $form->getConfig()->getOption('languageCode'));
        $content = $this->contentService->publishVersion($draft->versionInfo);

        $location = $this->locationService->loadLocation($content->contentInfo->mainLocationId);

        $redirectUrl = $form['redirectUrlAfterPublish']->getData() ?: $this->getSystemUrl($location, [$content->versionInfo->initialLanguageCode]);
        $event->setResponse(new RedirectResponse($redirectUrl));
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     * @param array $prioritizedLanguageList
     *
     * @return string
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    private function getSystemUrl(Location $location, array $prioritizedLanguageList): string
    {
        return $this->urlAliasService->reverseLookup(
            $location,
            null,
            true,
            $prioritizedLanguageList
        )->path;
    }

    /**
     * @param \EzSystems\RepositoryForms\Event\FormActionEvent $event
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function processCancel(FormActionEvent $event)
    {
        /** @var \EzSystems\RepositoryForms\Data\Content\ContentCreateData|\EzSystems\RepositoryForms\Data\Content\ContentUpdateData $data */
        $data = $event->getData();

        if ($data->isNew()) {
            $parentLocation = $this->locationService->loadLocation(
                $data->getLocationStructs()[0]->parentLocationId
            );
            $url = $this->getSystemUrl(
                $parentLocation,
                [$data->mainLanguageCode, $parentLocation->contentInfo->mainLanguageCode]
            );
            $response = new RedirectResponse($url);
            $event->setResponse($response);

            return;
        }

        $content = $data->contentDraft;
        $contentInfo = $content->contentInfo;
        $versionInfo = $data->contentDraft->getVersionInfo();

        // if there is only one version you have to remove whole content instead of a version itself
        if (1 === count($this->contentService->loadVersions($contentInfo))) {
            $parentLocation = $this->locationService->loadParentLocationsForDraftContent($versionInfo)[0];
            $redirectionLocationId = $parentLocation->id;
            $this->contentService->deleteContent($contentInfo);
        } else {
            $redirectionLocationId = $contentInfo->mainLocationId;
            $this->contentService->deleteVersion($versionInfo);
        }

        $locationToRedirect = $this->locationService->loadLocation($redirectionLocationId);

        $url = $this->getSystemUrl(
            $locationToRedirect,
            [$data->mainLanguageCode, $locationToRedirect->contentInfo->mainLanguageCode]
        );

        $event->setResponse(new RedirectResponse($url));
    }

    /**
     * @param \EzSystems\RepositoryForms\Event\FormActionEvent $event
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function processCreateDraft(FormActionEvent $event)
    {
        /** @var $createContentDraft \EzSystems\RepositoryForms\Data\Content\CreateContentDraftData */
        $createContentDraft = $event->getData();

        $contentInfo = $this->contentService->loadContentInfo($createContentDraft->contentId);
        $versionInfo = $this->contentService->loadVersionInfo($contentInfo, $createContentDraft->fromVersionNo);
        $contentDraft = $this->contentService->createContentDraft($contentInfo, $versionInfo);
        $referrerLocation = $event->getOption('referrerLocation');

        $contentEditUrl = $this->router->generate('ez_content_draft_edit', [
            'contentId' => $contentDraft->id,
            'versionNo' => $contentDraft->getVersionInfo()->versionNo,
            'language' => $contentDraft->contentInfo->mainLanguageCode,
            'locationId' => null !== $referrerLocation ? $referrerLocation->id : null,
        ]);
        $event->setResponse(new RedirectResponse($contentEditUrl));
    }

    /**
     * Saves content draft corresponding to $data.
     * Depending on the nature of $data (create or update data), the draft will either be created or simply updated.
     *
     * @param ContentStruct|\EzSystems\RepositoryForms\Data\Content\ContentCreateData|\EzSystems\RepositoryForms\Data\Content\ContentUpdateData $data
     * @param $languageCode
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Content
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     * @throws \eZ\Publish\API\Repository\Exceptions\ContentFieldValidationException
     * @throws \eZ\Publish\API\Repository\Exceptions\ContentValidationException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentException
     */
    private function saveDraft(ContentStruct $data, $languageCode)
    {
        $mainLanguageCode = $this->resolveMainLanguageCode($data);
        foreach ($data->fieldsData as $fieldDefIdentifier => $fieldData) {
            if ($mainLanguageCode != $languageCode && !$fieldData->fieldDefinition->isTranslatable) {
                continue;
            }

            $data->setField($fieldDefIdentifier, $fieldData->value, $languageCode);
        }

        if ($data->isNew()) {
            $contentDraft = $this->contentService->createContent($data, $data->getLocationStructs());
        } else {
            $contentDraft = $this->contentService->updateContent($data->contentDraft->getVersionInfo(), $data);
        }

        return $contentDraft;
    }

    /**
     * @param \EzSystems\RepositoryForms\Data\Content\ContentUpdateData|\EzSystems\RepositoryForms\Data\Content\ContentCreateData $data
     *
     * @return string
     *
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentException
     */
    private function resolveMainLanguageCode($data): string
    {
        if (!$data instanceof ContentUpdateData && !$data instanceof ContentCreateData) {
            throw new InvalidArgumentException(
                '$data',
                'expected type of ContentUpdateData or ContentCreateData'
            );
        }

        return $data->isNew()
            ? $data->mainLanguageCode
            : $data->contentDraft->getVersionInfo()->getContentInfo()->mainLanguageCode;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Content $content
     * @param \eZ\Publish\API\Repository\Values\Content\Location|null $referrerLocation
     * @param \EzSystems\RepositoryForms\Data\NewnessCheckable $data
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Location|null
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    private function resolveLocation(Content $content, ?Location $referrerLocation, NewnessCheckable $data): ?Location
    {
        if ($data->isNew() || (!$content->contentInfo->published && null === $content->contentInfo->mainLocationId)) {
            return null; // no location exists until new content is published
        }

        return $referrerLocation ?? $this->locationService->loadLocation($content->contentInfo->mainLocationId);
    }
}
