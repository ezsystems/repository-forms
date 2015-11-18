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
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\LocationService;
use EzSystems\RepositoryForms\Data\Mapper\ContentCreateMapper;
use EzSystems\RepositoryForms\Form\Type\Content\ContentEditType;
use Symfony\Component\HttpFoundation\Request;

class ContentEditController extends Controller
{
    /**
     * @var ContentTypeService
     */
    private $contentTypeService;

    /**
     * @var LocationService
     */
    private $locationService;

    public function __construct(ContentTypeService $contentTypeService, LocationService $locationService) {
        $this->contentTypeService = $contentTypeService;
        $this->locationService = $locationService;
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
            $data = $form->getData();
        }

        return $this->render('EzSystemsRepositoryFormsBundle:Content:content_create_no_draft.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function createAction($contentTypeId, $language, $parentLocationId)
    {
    }

    public function editAction($contentId, $version, $language = null)
    {
    }
}
