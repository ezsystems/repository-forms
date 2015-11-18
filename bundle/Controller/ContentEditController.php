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
use eZ\Publish\API\Repository\LocationService;
use EzSystems\RepositoryForms\Data\Mapper\ContentCreateMapper;
use EzSystems\RepositoryForms\Data\Mapper\ContentUpdateMapper;
use EzSystems\RepositoryForms\Form\ActionDispatcher\ActionDispatcherInterface;
use EzSystems\RepositoryForms\Form\Type\Content\ContentEditType;
use Symfony\Component\HttpFoundation\Request;

class ContentEditController extends Controller
{
    /**
     * @var ContentTypeService
     */
    private $contentTypeService;

    /**
     * @var ContentService
     */
    private $contentService;

    /**
     * @var LocationService
     */
    private $locationService;

    /**
     * @var ActionDispatcherInterface
     */
    private $contentActionDispatcher;

    public function __construct(
        ContentTypeService $contentTypeService,
        ContentService $contentService,
        LocationService $locationService,
        ActionDispatcherInterface $contentActionDispatcher
    ) {
        $this->contentTypeService = $contentTypeService;
        $this->locationService = $locationService;
        $this->contentActionDispatcher = $contentActionDispatcher;
        $this->contentService = $contentService;
    }

    public function createWithoutDraftAction($contentTypeId, $language, $parentLocationId, Request $request)
    {
        $contentType = $this->contentTypeService->loadContentType($contentTypeId);
        $data = (new ContentCreateMapper())->mapToFormData($contentType, [
            'mainLanguageCode' => $language,
            'parentLocation' => $this->locationService->newLocationCreateStruct($parentLocationId),
        ]);
        $form = $this->createForm(new ContentEditType(), $data, ['languageCode' => $language]);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->contentActionDispatcher->dispatchFormAction($form, $data, $form->getClickedButton()->getName());
            if ($response = $this->contentActionDispatcher->getResponse()) {
                return $response;
            }
        }

        return $this->render('EzSystemsRepositoryFormsBundle:Content:content_create_no_draft.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function createAction($contentTypeId, $language, $parentLocationId)
    {
    }

    public function editAction($contentId, $version, $language, Request $request)
    {
        // Create a new version if none is provided explicitly
        if ($version == 0) {
            $contentDraft = $this->contentService->createContentDraft($this->contentService->loadContentInfo($contentId));
        } else {
            $contentDraft = $this->contentService->loadContent($contentId, $language ? [$language] : null, $version);
        }

        $contentType = $this->contentTypeService->loadContentType($contentDraft->contentInfo->contentTypeId);
        $data = (new ContentUpdateMapper())->mapToFormData($contentDraft, [
            'languageCode' => $language,
            'contentType' => $contentType,
        ]);
        $form = $this->createForm(new ContentEditType(), $data, [
            'languageCode' => $language,
            'drafts_enabled' => true,
        ]);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->contentActionDispatcher->dispatchFormAction($form, $data, $form->getClickedButton()->getName());
            if ($response = $this->contentActionDispatcher->getResponse()) {
                return $response;
            }
        }

        return $this->render('EzSystemsRepositoryFormsBundle:Content:content_create_no_draft.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
