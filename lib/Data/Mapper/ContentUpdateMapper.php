<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Data\Mapper;

use eZ\Publish\API\Repository\Values\ValueObject;
use EzSystems\RepositoryForms\Data\Content\ContentUpdateData;
use EzSystems\RepositoryForms\Data\Content\FieldData;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContentUpdateMapper implements FormDataMapperInterface
{
    /**
     * Maps a ValueObject from eZ content repository to a data usable as underlying form data (e.g. create/update struct).
     *
     * @param ValueObject|\eZ\Publish\API\Repository\Values\Content\Content $contentDraft
     * @param array $params
     *
     * @return ContentUpdateData
     */
    public function mapToFormData(ValueObject $contentDraft, array $params = [])
    {
        $optionsResolver = new OptionsResolver();
        $this->configureOptions($optionsResolver);
        $params = $optionsResolver->resolve($params);

        $data = new ContentUpdateData(['contentDraft' => $contentDraft, 'contentType' => $params['contentType']]);
        $fields = $contentDraft->getFieldsByLanguage($params['languageCode']);
        foreach ($params['contentType']->fieldDefinitions as $fieldDef) {
            $field = $fields[$fieldDef->identifier];
            $data->addFieldData(new FieldData([
                'fieldDefinition' => $fieldDef,
                'field' => $field,
                'value' => $field->value,
            ]));
        }

        return $data;
    }

    private function configureOptions(OptionsResolver $optionsResolver)
    {
        $optionsResolver
            ->setRequired(['languageCode', 'contentType'])
            ->setAllowedTypes('contentType', '\eZ\Publish\API\Repository\Values\ContentType\ContentType');
    }
}
