<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */
namespace EzSystems\RepositoryForms\Form\Processor;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Values\Content\ContentCreateStruct;
use eZ\Publish\API\Repository\Values\Content\ContentStruct;
use eZ\Publish\Core\MVC\Symfony\Routing\UrlAliasRouter;
use EzSystems\RepositoryForms\Event\FormActionEvent;
use EzSystems\RepositoryForms\Event\RepositoryFormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Listens for and processes RepositoryForm events: publish, remove draft, save draft...
 */
class ContentFormProcessor implements EventSubscriberInterface
{
    /**
     * @var \eZ\Publish\API\Repository\ContentService
     */
    private $contentService;

    /**
     * @var \eZ\Publish\API\Repository\LocationService
     */
    private $locationService;

    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    private $router;

    public function __construct(ContentService $contentService, RouterInterface $router, LocationService $locationService)
    {
        $this->contentService = $contentService;
        $this->locationService = $locationService;
        $this->router = $router;
    }

    public static function getSubscribedEvents()
    {
        return [
            RepositoryFormEvents::CONTENT_PUBLISH => ['processPublish', 10],
            RepositoryFormEvents::CONTENT_CANCEL => ['processRemoveDraft', 10],
            RepositoryFormEvents::CONTENT_SAVE_DRAFT => ['processSaveDraft', 10],
        ];
    }

    public function processSaveDraft(FormActionEvent $event)
    {
        /** @var \EzSystems\RepositoryForms\Data\Content\ContentCreateData|\EzSystems\RepositoryForms\Data\Content\ContentUpdateData $data */
        $data = $event->getData();
        $form = $event->getForm();

        $formConfig = $form->getConfig();
        $languageCode = $formConfig->getOption('languageCode');
        $draft = $this->saveDraft($data, $languageCode);

        $defaultUrl = $this->router->generate('ez_content_edit', [
            'contentId' => $draft->id,
            'version' => $draft->getVersionInfo()->versionNo,
            'language' => $languageCode,
        ]);
        $event->setResponse(new RedirectResponse($formConfig->getAction() ?: $defaultUrl));
    }

    public function processPublish(FormActionEvent $event)
    {
        /** @var \eZ\Publish\API\Repository\Values\Content\ContentCreateStruct $data */
        $data = $event->getData();
        $form = $event->getForm();
        $formConfig = $form->getConfig();

        $draft = $this->contentService->createContent(
            $data,
            [$this->locationService->newLocationCreateStruct($formConfig->getOption('parentLocationId'))]
        );
        $this->saveDraft($data, $formConfig->getOption('languageCode'));
        $content = $this->contentService->publishVersion($draft->versionInfo);

        // Redirect to the provided URL. Defaults to URLAlias of the published content.
        $redirectUrl = $form['redirectUrlAfterPublish']->getData() ?: $this->router->generate(
            UrlAliasRouter::URL_ALIAS_ROUTE_NAME,
            ['contentId' => $content->id],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $event->setResponse(new RedirectResponse($redirectUrl));
    }

    public function processRemoveDraft(FormActionEvent $event)
    {
        /** @var \EzSystems\RepositoryForms\Data\Content\ContentCreateData|\EzSystems\RepositoryForms\Data\Content\ContentUpdateData $data */
        $data = $event->getData();
        $form = $event->getForm();

        if ($data->isNew()) {
            return;
        }

        $this->contentService->deleteVersion($data->contentDraft->getVersionInfo());
        $url = $this->router->generate(
            UrlAliasRouter::URL_ALIAS_ROUTE_NAME,
            ['contentId' => $data->contentDraft->id],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $event->setResponse(new RedirectResponse($url));
    }

    /**
     * Saves content draft corresponding to $data.
     * Depending on the nature of $data (create or update data), the draft will either be created or simply updated.
     *
     * @param ContentStruct|\EzSystems\RepositoryForms\Data\Content\ContentCreateData|\EzSystems\RepositoryForms\Data\Content\ContentUpdateData $data
     * @param $languageCode
     * @return \eZ\Publish\API\Repository\Values\Content\Content
     */
    private function saveDraft(ContentStruct $data, $languageCode)
    {
        foreach ($data->fieldsData as $fieldDefIdentifier => $fieldData) {
            $data->setField($fieldDefIdentifier, $fieldData->value, $languageCode);
        }

        if ($data->isNew()) {
            $contentDraft = $this->contentService->createContent($data, $data->getLocationStructs());
        } else {
            $contentDraft = $this->contentService->updateContent($data->contentDraft->getVersionInfo(), $data);
        }

        return $contentDraft;
    }
}
