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
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\Core\Repository\Values\ContentType\ContentTypeCreateStruct;
use EzSystems\RepositoryForms\Data\Mapper\ContentTypeDraftMapper;
use EzSystems\RepositoryForms\Event\FormActionEvent;
use EzSystems\RepositoryForms\Event\RepositoryFormEvents;
use Symfony\Component\HttpFoundation\Request;

class ContentTypeController extends Controller
{
    public function createContentTypeAction($contentTypeGroupId, $languageCode = null)
    {
        $languageCode = $languageCode ?: $this->getConfigResolver()->getParameter('languages')[0];
        $contentTypeService = $this->getRepository()->getContentTypeService();
        $contentTypeGroup = $contentTypeService->loadContentTypeGroup($contentTypeGroupId);

        $contentTypeCreateStruct = new ContentTypeCreateStruct([
            'identifier' => 'new_content_type',
            'mainLanguageCode' => $languageCode,
            'names' => [$languageCode => 'New ContentType'],
        ]);
        $contentTypeDraft = $contentTypeService->createContentType($contentTypeCreateStruct, [$contentTypeGroup]);

        return $this->redirectToRoute(
            'contenttype/update',
            ['contentTypeId' => $contentTypeDraft->id, 'languageCode' => $languageCode]
        );
    }

    public function updateContentTypeAction(Request $request, $contentTypeId, $languageCode = null)
    {
        $languageCode = $languageCode ?: $this->getConfigResolver()->getParameter('languages')[0];
        $contentTypeService = $this->getRepository()->getContentTypeService();
        // First try to load the draft.
        // If it doesn't exist, create it.
        try {
            $contentTypeDraft = $contentTypeService->loadContentTypeDraft($contentTypeId);
        } catch (NotFoundException $e) {
            $contentTypeDraft = $contentTypeService->createContentTypeDraft(
                $contentTypeService->loadContentType($contentTypeId)
            );
        }

        $contentTypeData = (new ContentTypeDraftMapper())->mapToFormData($contentTypeDraft);
        $form = $this->createForm('ezrepoforms_contenttype_update', $contentTypeData, [
            'languageCode' => $languageCode,
        ]);

        // Synchronize form and data.
        $form->handleRequest($request);
        if ($form->isValid()) {
            $actionDispatcher = $this->get('ezrepoforms.action_dispatcher.content_type');
            $actionDispatcher->dispatchFormAction(
                $form, $contentTypeData, $form->getClickedButton()->getName(),
                ['languageCode' => $languageCode]
            );

            if ($response = $actionDispatcher->getResponse()) {
                return $response;
            }

            return $this->redirectToRoute('contenttype/update', ['contentTypeId' => $contentTypeId, 'languageCode' => $languageCode]);
        }

        return $this->render('EzSystemsRepositoryFormsBundle:ContentType:update_content_type.html.twig', [
            'form' => $form->createView(),
            'contentTypeName' => $contentTypeDraft->getName($languageCode),
            'contentTypeDraft' => $contentTypeDraft,
            'languageCode' => $languageCode,
            'fieldTypeMapperRegistry' => $this->get('ezrepoforms.field_type_form_mapper.registry')
        ]);
    }
}
