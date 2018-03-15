<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryFormsBundle\Controller;

use eZ\Bundle\EzPublishCoreBundle\Controller;
use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use EzSystems\RepositoryForms\Content\View\ContentCreateDraftView;
use EzSystems\RepositoryForms\Content\View\ContentCreateSuccessView;
use EzSystems\RepositoryForms\Content\View\ContentCreateView;
use EzSystems\RepositoryForms\Content\View\ContentEditSuccessView;
use EzSystems\RepositoryForms\Content\View\ContentEditView;
use EzSystems\RepositoryForms\Data\Content\CreateContentDraftData;
use EzSystems\RepositoryForms\Form\ActionDispatcher\ActionDispatcherInterface;
use EzSystems\RepositoryForms\Form\Type\Content\ContentDraftCreateType;
use Symfony\Component\HttpFoundation\Request;

class ContentEditController extends Controller
{
    /** @var ContentTypeService */
    private $contentTypeService;

    /** @var ContentService */
    private $contentService;

    /** @var ActionDispatcherInterface */
    private $contentActionDispatcher;

    public function __construct(
        ContentTypeService $contentTypeService,
        ContentService $contentService,
        ActionDispatcherInterface $contentActionDispatcher
    ) {
        $this->contentTypeService = $contentTypeService;
        $this->contentService = $contentService;
        $this->contentActionDispatcher = $contentActionDispatcher;
    }

    /**
     * Displays and processes a content creation form. Showing the form does not create a draft in the repository.
     *
     * @param \EzSystems\RepositoryForms\Content\View\ContentCreateView $view
     *
     * @return \EzSystems\RepositoryForms\Content\View\ContentCreateView
     */
    public function createWithoutDraftAction(ContentCreateView $view): ContentCreateView
    {
        return $view;
    }

    /**
     * @param \EzSystems\RepositoryForms\Content\View\ContentCreateSuccessView $view
     *
     * @return \EzSystems\RepositoryForms\Content\View\ContentCreateSuccessView
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     */
    public function createWithoutDraftSuccessAction(ContentCreateSuccessView $view): ContentCreateSuccessView
    {
        return $view;
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

        if ($form->isValid() && null !== $form->getClickedButton()) {
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
     * @param \EzSystems\RepositoryForms\Content\View\ContentEditView $view
     *
     * @return \EzSystems\RepositoryForms\Content\View\ContentEditView
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     */
    public function editVersionDraftAction(ContentEditView $view): ContentEditView
    {
        return $view;
    }

    /**
     * @param \EzSystems\RepositoryForms\Content\View\ContentEditSuccessView $view
     *
     * @return \EzSystems\RepositoryForms\Content\View\ContentEditSuccessView
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     */
    public function editVersionDraftSuccessAction(ContentEditSuccessView $view): ContentEditSuccessView
    {
        return $view;
    }

    /**
     * Shows a content draft editing form.
     *
     * @deprecated In 2.1 and will be removed in 3.0. Please use `editVersionDraftAction()` instead.
     *
     * @param int $contentId ContentType id to create
     * @param int $versionNo Version number the version should be created from. Defaults to the currently published one.
     * @param string $language Language code to create the version in (eng-GB, ger-DE, ...))
     * @param int|null $locationId
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editContentDraftAction(
        $contentId,
        $versionNo = null,
        $language = null,
        $locationId = null
    ) {
        return $this->forward('ez_content_edit:editVersionDraftAction', [
            'contentId' => $contentId,
            'versionNo' => $versionNo,
            'languageCode' => $language,
            'locationId' => $locationId,
        ]);
    }
}
