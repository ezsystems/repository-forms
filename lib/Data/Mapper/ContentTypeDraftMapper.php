<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 *
 * @version //autogentag//
 */
namespace EzSystems\RepositoryForms\Data\Mapper;

use eZ\Publish\API\Repository\Values\ValueObject;
use EzSystems\RepositoryForms\Data\ContentTypeData;
use EzSystems\RepositoryForms\Data\FieldDefinitionData;

class ContentTypeDraftMapper implements FormDataMapperInterface
{
    /**
     * Maps a ValueObject from eZ content repository to a data usable as underlying form data (e.g. create/update struct).
     *
     * @param ValueObject|\eZ\Publish\API\Repository\Values\ContentType\ContentTypeDraft $contentTypeDraft
     * @param array $params
     *
     * @return ContentTypeData
     */
    public function mapToFormData(ValueObject $contentTypeDraft, array $params = [])
    {
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

        return $contentTypeData;
    }
}
