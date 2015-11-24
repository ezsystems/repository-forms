<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 *
 * @version //autogentag//
 */
namespace EzSystems\RepositoryForms\FieldType\Mapper;

use EzSystems\RepositoryForms\Data\Content\FieldData;
use EzSystems\RepositoryForms\Data\FieldDefinitionData;
use EzSystems\RepositoryForms\FieldType\FieldTypeFormMapperInterface;
use Symfony\Component\Form\FormInterface;

class TextBlockFormMapper extends AbstractMapper
{
    public function mapFieldDefinitionForm(FormInterface $fieldDefinitionForm, FieldDefinitionData $data)
    {
        $fieldDefinitionForm
            ->add(
                'textRows', 'integer', [
                    'required' => false,
                    'property_path' => 'fieldSettings[textRows]',
                    'label' => 'field_definition.eztext.text_rows',
                ]
            );
    }

    protected function getContentFormFieldType()
    {
        return 'textarea';
    }

    protected function getContentFormFieldTypeOptions(FormInterface $fieldForm, FieldData $data)
    {
        return [
            'attr' => ['rows' => $data->fieldDefinition->fieldSettings['textRows']]
        ];
    }
}
