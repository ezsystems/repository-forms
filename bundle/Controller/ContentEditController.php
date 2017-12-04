<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */
namespace EzSystems\RepositoryFormsBundle\Controller;

use eZ\Bundle\EzPublishCoreBundle\Controller;
use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\LanguageService;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Values\Content\VersionInfo;
use eZ\Publish\Core\Base\Exceptions\BadStateException;
use EzSystems\RepositoryForms\Content\View\ContentCreateDraftView;
use EzSystems\RepositoryForms\Content\View\ContentCreateView;
use EzSystems\RepositoryForms\Content\View\ContentEditView;
use EzSystems\RepositoryForms\Data\Content\CreateContentDraftData;
use EzSystems\RepositoryForms\Data\Mapper\ContentCreateMapper;
use EzSystems\RepositoryForms\Data\Mapper\ContentUpdateMapper;
use EzSystems\RepositoryForms\Form\ActionDispatcher\ActionDispatcherInterface;
use EzSystems\RepositoryForms\Form\Type\Content\ContentDraftCreateType;
use EzSystems\RepositoryForms\Form\Type\Content\ContentEditType;
use Symfony\Component\HttpFoundation\Request;

class ContentEditController extends Controller
{
    /** @var ContentTypeService */
    private $contentTypeService;

    /** @var ContentService */
    private $contentService;

    /** @var LocationService */
    private $locationService;

    /** @var LanguageService */
    private $languageService;

    /** @var ActionDispatcherInterface */
    private $contentActionDispatcher;

    /**
     * @var string
     *
     * @deprecated Deprecated since 1.10 and will be removed in 2.0. See setPagelayout().
     */
    private $pagelayout;

    public function __construct(
        ContentTypeService $contentTypeService,
        ContentService $contentService,
        LocationService $locationService,
        LanguageService $languageService,
        ActionDispatcherInterface $contentActionDispatcher
    ) {
        $this->contentTypeService = $contentTypeService;
        $this->locationService = $locationService;
        $this->languageService = $languageService;
        $this->contentActionDispatcher = $contentActionDispatcher;
        $this->contentService = $contentService;
    }

    /**
     * Displays and processes a content creation form. Showing the form does not create a draft in the repository.
     *
     * @param int $contentTypeIdentifier ContentType id to create
     * @param string $language Language code to create the content in (eng-GB, ger-DE, ...))
     * @param int $parentLocationId Location the content should be a child of
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \EzSystems\RepositoryForms\Content\View\ContentCreateView|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentType
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    public function createWithoutDraftAction($contentTypeIdentifier, $language, $parentLocationId, Request $request)
    {
        $language = $this->languageService->loadLanguage($language);
        $parentLocation = $this->locationService->loadLocation($parentLocationId);
        $contentType = $this->contentTypeService->loadContentTypeByIdentifier($contentTypeIdentifier);
        $data = (new ContentCreateMapper())->mapToFormData($contentType, [
            'mainLanguageCode' => $language->languageCode,
            'parentLocation' => $this->locationService->newLocationCreateStruct($parentLocationId),
        ]);
        $form = $this->createForm(ContentEditType::class, $data, [
            'languageCode' => $language->languageCode,
            'mainLanguageCode' => $language->languageCode,
            'drafts_enabled' => true,
        ]);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->contentActionDispatcher->dispatchFormAction($form, $data, $form->getClickedButton()->getName());
            if ($response = $this->contentActionDispatcher->getResponse()) {
                return $response;
            }
        }

        return new ContentCreateView(null, [
            'form' => $form->createView(),
            'language' => $language,
            'contentType' => $contentType,
            'parentLocation' => $parentLocation,
        ]);
    }

    /**
     * Displays a draft creation form that creates a content draft from an existing content.
     *
     * @param mixed $contentId
     * @param int $fromVersionNo
     * @param string $fromLanguage
     * @param string $toLanguage
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \EzSystems\RepositoryForms\Content\View\ContentCreateDraftView|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentType
     */
    public function createContentDraftAction(
        $contentId,
        $fromVersionNo = null,
        $fromLanguage = null,
        $toLanguage = null,
        Request $request
    ) {
        $createContentDraft = new CreateContentDraftData();
        $contentInfo = null;
        $contentType = null;

        if ($contentId !== null) {
            $createContentDraft->contentId = $contentId;

            $contentInfo = $this->contentService->loadContentInfo($contentId);
            $contentType = $this->contentTypeService->loadContentType($contentInfo->contentTypeId);
            $createContentDraft->fromVersionNo = $fromVersionNo ?: $contentInfo->currentVersionNo;
            $createContentDraft->fromLanguage = $fromLanguage ?: $contentInfo->mainLanguageCode;
        }

        $form = $this->createForm(
            ContentDraftCreateType::class,
            $createContentDraft,
            [
                'action' => $this->generateUrl('ez_content_draft_create'),
            ]
        );

        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->contentActionDispatcher->dispatchFormAction($form, $createContentDraft, $form->getClickedButton()->getName());
            if ($response = $this->contentActionDispatcher->getResponse()) {
                return $response;
            }
        }

        return new ContentCreateDraftView(null, [
            'form' => $form->createView(),
            'contentInfo' => $contentInfo,
            'contentType' => $contentType,
        ]);
    }

    /**
     * Shows a content draft editing form.
     *
     * @param int $contentId ContentType id to create
     * @param int $versionNo Version number the version should be created from. Defaults to the currently published one.
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param string $language Language code to create the version in (eng-GB, ger-DE, ...))
     *
     * @return \EzSystems\RepositoryForms\Content\View\ContentEditView|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentType
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\Core\Base\Exceptions\BadStateException If the version isn't editable, or if there is no editable version.
     */
    public function editContentDraftAction($contentId, $versionNo = null, Request $request, $language = null)
    {
        $draft = $this->contentService->loadContent($contentId, [$language], $versionNo);
        if ($draft->getVersionInfo()->status !== VersionInfo::STATUS_DRAFT) {
            throw new BadStateException('Version status', 'status is not draft');
        }

        $language = $language ?: $draft->getVersionInfo()->getContentInfo()->mainLanguageCode;
        $language = $this->languageService->loadLanguage($language);
        $contentType = $this->contentTypeService->loadContentType($draft->contentInfo->contentTypeId);

        $contentUpdate = (new ContentUpdateMapper())->mapToFormData(
            $draft,
            [
                'languageCode' => $language->languageCode,
                'contentType' => $this->contentTypeService->loadContentType($draft->contentInfo->contentTypeId),
            ]
        );
        $form = $this->createForm(
            ContentEditType::class,
            $contentUpdate,
            [
                'languageCode' => $language->languageCode,
                'mainLanguageCode' => $draft->contentInfo->mainLanguageCode,
                'drafts_enabled' => true,
            ]
        );
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->contentActionDispatcher->dispatchFormAction($form, $contentUpdate, $form->getClickedButton()->getName());
            if ($response = $this->contentActionDispatcher->getResponse()) {
                return $response;
            }
        }

        return new ContentEditView(null, [
            'form' => $form->createView(),
            'language' => $language,
            'content' => $draft,
            'contentType' => $contentType,
        ]);
    }

    /**
     * @param string $pagelayout
     * @return ContentEditController
     *
     * @deprecated Deprecated since 1.10 and will be removed in 2.0. Pagelayout is injected via ViewTemplatesListener.
     */
    public function setPagelayout($pagelayout)
    {
        $this->pagelayout = $pagelayout;

        return $this;
    }
}
