<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Data\Mapper;

use eZ\Publish\API\Repository\Values\Content\Language;
use eZ\Publish\API\Repository\Values\ValueObject;
use EzSystems\RepositoryForms\Data\ContentTypeData;
use EzSystems\RepositoryForms\Data\FieldDefinitionData;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
        $optionsResolver = new OptionsResolver();
        $this->configureOptions($optionsResolver);
        $params = $optionsResolver->resolve($params);

        /** @var \eZ\Publish\API\Repository\Values\Content\Language $language */
        $language = $params['language'];

        /** @var \eZ\Publish\API\Repository\Values\Content\Language|null $baseLanguage */
        $baseLanguage = $params['baseLanguage'];

        $contentTypeData = new ContentTypeData(['contentTypeDraft' => $contentTypeDraft]);
        if (!$contentTypeData->isNew()) {
            $contentTypeData->identifier = $contentTypeDraft->identifier;
        }

        $contentTypeData->remoteId = $contentTypeDraft->remoteId;
        $contentTypeData->urlAliasSchema = $contentTypeDraft->urlAliasSchema;
        $contentTypeData->nameSchema = $contentTypeDraft->nameSchema;
        $contentTypeData->isContainer = $contentTypeDraft->isContainer;
        $contentTypeData->mainLanguageCode = $contentTypeDraft->mainLanguageCode;
        $contentTypeData->defaultSortField = $contentTypeDraft->defaultSortField;
        $contentTypeData->defaultSortOrder = $contentTypeDraft->defaultSortOrder;
        $contentTypeData->defaultAlwaysAvailable = $contentTypeDraft->defaultAlwaysAvailable;
        $contentTypeData->names = $contentTypeDraft->getNames();
        $contentTypeData->descriptions = $contentTypeDraft->getDescriptions();

        $contentTypeData->usedLanguageCode = $language ? $language->languageCode : $contentTypeDraft->mainLanguageCode;

        if ($baseLanguage && $language) {
            $contentTypeData->names[$language->languageCode] = $contentTypeDraft->getName($baseLanguage->languageCode);
            $contentTypeData->descriptions[$language->languageCode] = $contentTypeDraft->getDescription($baseLanguage->languageCode);
        }

        foreach ($contentTypeDraft->fieldDefinitions as $fieldDef) {
            $names = $fieldDef->getNames();
            $descriptions = $fieldDef->getDescriptions();
            if ($baseLanguage && $language) {
                $names[$language->languageCode] = $fieldDef->getName($baseLanguage->languageCode);
                $descriptions[$language->languageCode] = $fieldDef->getDescription($baseLanguage->languageCode);
            }

            $contentTypeData->addFieldDefinitionData(new FieldDefinitionData([
                'fieldDefinition' => $fieldDef,
                'contentTypeData' => $contentTypeData,
                'identifier' => $fieldDef->identifier,
                'names' => $names,
                'descriptions' => $descriptions,
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
        $contentTypeData->sortFieldDefinitions();

        return $contentTypeData;
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $optionsResolver
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    private function configureOptions(OptionsResolver $optionsResolver)
    {
        $optionsResolver
            ->setDefined(['language'])
            ->setDefined(['baseLanguage'])
            ->setAllowedTypes('baseLanguage', ['null', Language::class])
            ->setAllowedTypes('language', Language::class);
    }
}
