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
use EzSystems\RepositoryForms\Data\Content\ContentCreateData;
use EzSystems\RepositoryForms\Data\Content\FieldData;
use EzSystems\RepositoryForms\Form\Type\Content\ContentEditType;
use Symfony\Component\HttpFoundation\Request;

class ContentEditController extends Controller
{
    public function createWithoutDraftAction($contentTypeId, $language, $parentLocationId, Request $request)
    {
        $contentTypeService = $this->getRepository()->getContentTypeService();
        $contentType = $contentTypeService->loadContentType($contentTypeId);
        $data = new ContentCreateData(['contentType' => $contentType, 'mainLanguageCode' => $language]);
        foreach ($contentType->fieldDefinitions as $fieldDef) {
            $data->addFieldData(new FieldData(['fieldDefinition' => $fieldDef]));
        }

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
