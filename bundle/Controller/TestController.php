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
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinitionCreateStruct;
use eZ\Publish\Core\Repository\Values\ContentType\ContentTypeCreateStruct;
use EzSystems\RepositoryForms\Data\ContentTypeData;
use EzSystems\RepositoryForms\Data\FieldDefinitionData;
use Symfony\Component\HttpFoundation\Request;

class TestController extends Controller
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
        $contentTypeDraft = $contentTypeService->loadContentTypeDraft($contentTypeId);

        // TODO: This must be done in a dedicated service
        $contentTypeData = new ContentTypeData([
            'contentTypeDraft' => $contentTypeDraft,
            'identifier' => $contentTypeDraft->identifier,
            'remoteId' => $contentTypeDraft->remoteId,
            'urlAliasSchema' => $contentTypeDraft->urlAliasSchema,
            'nameSchema' => $contentTypeDraft->nameSchema,
            'isContainer' => $contentTypeDraft->isContainer,
            'mainLanguageCode' => $contentTypeDraft->mainLanguageCode,
            'defaultSortField' => $contentTypeDraft->defaultSortField,
            'defaultSortOrder' => $contentTypeDraft->defaultSortOrder,
            'defaultAlwaysAvailable' => $contentTypeDraft->defaultAlwaysAvailable,
            'names' => $contentTypeDraft->getNames(),
            'descriptions' => $contentTypeDraft->getDescriptions(),
        ]);
        foreach ($contentTypeDraft->fieldDefinitions as $fieldDef) {
            $contentTypeData->addFieldDefinitionData(new FieldDefinitionData([
                'fieldDefinition' => $fieldDef,
                'contentTypeData' => $contentTypeData,
                'identifier' => $fieldDef->identifier,
                'names' => $fieldDef->getNames(),
                'descriptions' => $fieldDef->getDescriptions(),
                'fieldGroup' => $fieldDef->fieldGroup,
                'position' => $fieldDef->position,
                'isTranslatable' => $fieldDef->isTranslatable,
                'isRequired' => $fieldDef->isRequired,
                'isInfoCollector' => $fieldDef->isInfoCollector,
                'validatorConfiguration' => $fieldDef->getValidatorConfiguration(),
                'fieldSettings' => $fieldDef->getFieldSettings(),
                'defaultValue' => $fieldDef->defaultValue,
                'isSearchable' => $fieldDef->isSearchable,
            ]));
        }

        $form = $this->createForm('ezrepoforms_contenttype_update', $contentTypeData, [
            'languageCode' => $languageCode,
        ]);

        // Synchronize form and data.
        $form->handleRequest($request);
        if ($form->isValid()) {
            $clickedButton = $form->getClickedButton();
            // Different actions may occur depending on clicked submit button.
            // TODO: Should ideally dispatched to form submitters
            switch ($clickedButton->getName()) {
                // Add a FieldDefinition
                case 'addFieldDefinition':
                    $fieldTypeIdentifier = $form->get('fieldTypeSelection')->getData();
                    $fieldDefCreateStruct = new FieldDefinitionCreateStruct([
                        'fieldTypeIdentifier' => $fieldTypeIdentifier,
                        'identifier' => sprintf('new_%s_%d', $fieldTypeIdentifier, count($contentTypeDraft->fieldDefinitions) + 1),
                        'names' => [$languageCode => 'New FieldDefinition'],
                    ]);
                    $contentTypeService->addFieldDefinition($contentTypeDraft, $fieldDefCreateStruct);
                    break;

                case 'saveContentType':
                    foreach ($contentTypeData->fieldDefinitionsData as $fieldDefData) {
                        $contentTypeService->updateFieldDefinition($contentTypeDraft, $fieldDefData->fieldDefinition, $fieldDefData);
                    }
                    $contentTypeService->updateContentTypeDraft($contentTypeDraft, $contentTypeData);
                    break;
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
