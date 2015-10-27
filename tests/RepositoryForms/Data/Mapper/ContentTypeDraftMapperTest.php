<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 *
 * @version //autogentag//
 */
namespace EzSystems\RepositoryForms\Tests\Data\Mapper;

use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\Core\Repository\Values\ContentType\ContentType;
use eZ\Publish\Core\Repository\Values\ContentType\ContentTypeDraft;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;
use EzSystems\RepositoryForms\Data\ContentTypeData;
use EzSystems\RepositoryForms\Data\FieldDefinitionData;
use EzSystems\RepositoryForms\Data\Mapper\ContentTypeDraftMapper;
use PHPUnit_Framework_TestCase;

class ContentTypeDraftMapperTest extends PHPUnit_Framework_TestCase
{
    public function testMapToFormData()
    {
        $fieldDef1 = new FieldDefinition([
            'identifier' => 'identifier1',
            'fieldTypeIdentifier' => 'ezstring',
            'names' => ['fre-FR' => 'foo'],
            'descriptions' => ['fre-FR' => 'some description'],
            'fieldGroup' => 'foo',
            'position' => 0,
            'isTranslatable' => true,
            'isRequired' => true,
            'isInfoCollector' => false,
            'validatorConfiguration' => ['validator' => 'config'],
            'fieldSettings' => ['field' => 'settings'],
            'defaultValue' => $this->getMockForAbstractClass('\eZ\Publish\Core\FieldType\Value'),
            'isSearchable' => true,
        ]);
        $fieldDef2 = new FieldDefinition([
            'identifier' => 'identifier2',
            'fieldTypeIdentifier' => 'eztext',
            'names' => ['fre-FR' => 'foo2'],
            'descriptions' => ['fre-FR' => 'some description 2'],
            'fieldGroup' => 'foo2',
            'position' => 15,
            'isTranslatable' => false,
            'isRequired' => false,
            'isInfoCollector' => true,
            'validatorConfiguration' => ['validator2' => 'config'],
            'fieldSettings' => ['field2' => 'settings'],
            'defaultValue' => null,
            'isSearchable' => false,
        ]);
        $fieldDef3 = new FieldDefinition([
            'identifier' => 'identifiea3',
            'fieldTypeIdentifier' => 'eztext',
            'names' => ['fre-FR' => 'foo3'],
            'descriptions' => ['fre-FR' => 'some description 3'],
            'fieldGroup' => 'foo3',
            'position' => 15,
            'isTranslatable' => false,
            'isRequired' => false,
            'isInfoCollector' => true,
            'validatorConfiguration' => ['validator3' => 'config'],
            'fieldSettings' => ['field3' => 'settings'],
            'defaultValue' => null,
            'isSearchable' => false,
        ]);
        $fieldDefs = [$fieldDef1, $fieldDef2, $fieldDef3];

        $identifier = 'identifier';
        $remoteId = 'remoteId';
        $urlAliasSchema = 'urlAliasSchema';
        $nameSchema = 'nameSchema';
        $isContainer = true;
        $mainLanguageCode = 'fre-FR';
        $defaultSortField = Location::SORT_FIELD_NAME;
        $defaultSortOrder = Location::SORT_ORDER_ASC;
        $defaultAlwaysAvailable = true;
        $names = ['fre-FR' => 'Français', 'eng-GB' => 'English'];
        $descriptions = ['fre-FR' => 'Vive le sucre !!!', 'eng-GB' => 'Sugar rules!!!'];
        $contentTypeDraft = new ContentTypeDraft([
            'innerContentType' => new ContentType([
                'fieldDefinitions' => $fieldDefs,
                'identifier' => $identifier,
                'remoteId' => $remoteId,
                'urlAliasSchema' => $urlAliasSchema,
                'nameSchema' => $nameSchema,
                'isContainer' => $isContainer,
                'mainLanguageCode' => $mainLanguageCode,
                'defaultSortField' => $defaultSortField,
                'defaultSortOrder' => $defaultSortOrder,
                'defaultAlwaysAvailable' => $defaultAlwaysAvailable,
                'names' => $names,
                'descriptions' => $descriptions,
            ]),
        ]);

        $expectedContentTypeData = new ContentTypeData([
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
        $expectedFieldDefData1 = new FieldDefinitionData([
            'fieldDefinition' => $fieldDef1,
            'contentTypeData' => $expectedContentTypeData,
            'identifier' => $fieldDef1->identifier,
            'names' => $fieldDef1->names,
            'descriptions' => $fieldDef1->descriptions,
            'fieldGroup' => $fieldDef1->fieldGroup,
            'position' => $fieldDef1->position,
            'isTranslatable' => $fieldDef1->isTranslatable,
            'isRequired' => $fieldDef1->isRequired,
            'isInfoCollector' => $fieldDef1->isInfoCollector,
            'validatorConfiguration' => $fieldDef1->validatorConfiguration,
            'fieldSettings' => $fieldDef1->fieldSettings,
            'defaultValue' => $fieldDef1->defaultValue,
            'isSearchable' => $fieldDef1->isSearchable,
        ]);
        $expectedContentTypeData->addFieldDefinitionData($expectedFieldDefData1);
        $expectedFieldDefData3 = new FieldDefinitionData([
            'fieldDefinition' => $fieldDef3,
            'contentTypeData' => $expectedContentTypeData, 'identifier' => $fieldDef3->identifier,
            'names' => $fieldDef3->names,
            'descriptions' => $fieldDef3->descriptions,
            'fieldGroup' => $fieldDef3->fieldGroup,
            'position' => $fieldDef3->position,
            'isTranslatable' => $fieldDef3->isTranslatable,
            'isRequired' => $fieldDef3->isRequired,
            'isInfoCollector' => $fieldDef3->isInfoCollector,
            'validatorConfiguration' => $fieldDef3->validatorConfiguration,
            'fieldSettings' => $fieldDef3->fieldSettings,
            'defaultValue' => $fieldDef3->defaultValue,
            'isSearchable' => $fieldDef3->isSearchable,
        ]);
        $expectedContentTypeData->addFieldDefinitionData($expectedFieldDefData3);
        $expectedFieldDefData2 = new FieldDefinitionData([
            'fieldDefinition' => $fieldDef2,
            'contentTypeData' => $expectedContentTypeData, 'identifier' => $fieldDef2->identifier,
            'names' => $fieldDef2->names,
            'descriptions' => $fieldDef2->descriptions,
            'fieldGroup' => $fieldDef2->fieldGroup,
            'position' => $fieldDef2->position,
            'isTranslatable' => $fieldDef2->isTranslatable,
            'isRequired' => $fieldDef2->isRequired,
            'isInfoCollector' => $fieldDef2->isInfoCollector,
            'validatorConfiguration' => $fieldDef2->validatorConfiguration,
            'fieldSettings' => $fieldDef2->fieldSettings,
            'defaultValue' => $fieldDef2->defaultValue,
            'isSearchable' => $fieldDef2->isSearchable,
        ]);
        $expectedContentTypeData->addFieldDefinitionData($expectedFieldDefData2);

        self::assertEquals($expectedContentTypeData, (new ContentTypeDraftMapper())->mapToFormData($contentTypeDraft));
    }
}
