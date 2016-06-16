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
use EzSystems\RepositoryForms\FieldType\DataTransformer\MultiSelectionValueTransformer;
use EzSystems\RepositoryForms\FieldType\DataTransformer\SingleSelectionValueTransformer;
use EzSystems\RepositoryForms\FieldType\FieldTypeFormMapperInterface;
use EzSystems\RepositoryForms\FieldType\FieldValueFormMapperInterface;
use Symfony\Component\Form\FormInterface;

class SelectionFormMapper implements FieldTypeFormMapperInterface, FieldValueFormMapperInterface
{
    /**
     * Selection items can be added and removed, the collection field type is used for this.
     * - An empty field is always present, if this is filled it will become a new entry.
     * - If a filled field is cleared the entry will be removed.
     * - Only one new entry can be added per page load (while any number can be removed).
     *   This can be improved using a template override with javascript code.
     * - The prototype_name option is for the empty field which is used for new items. If not
     *   using javascript, it must be unique.
     */
    public function mapFieldDefinitionForm(FormInterface $fieldDefinitionForm, FieldDefinitionData $data)
    {
        $fieldDefinitionForm
            ->add('isMultiple', 'checkbox', [
                'required' => false,
                'property_path' => 'fieldSettings[isMultiple]',
                'label' => 'field_definition.ezselection.is_multiple',
            ])
            ->add('options', 'collection', [
                'type' => 'text',
                'options' => ['required' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'delete_empty' => true,
                'prototype' => true,
                'prototype_name' => '__prototype__',
                'required' => false,
                'property_path' => 'fieldSettings[options]',
                'label' => 'field_definition.ezselection.options',
            ]);
    }

    public function mapFieldValueForm(FormInterface $fieldForm, FieldData $data)
    {
        $fieldDefinition = $data->fieldDefinition;
        $formConfig = $fieldForm->getConfig();
        $label = $fieldDefinition->getName($formConfig->getOption('languageCode')) ?: reset($fieldDefinition->getNames());

        $fieldForm
            ->add(
                $formConfig->getFormFactory()->createBuilder()
                    ->create(
                        'value',
                        'choice',
                        [
                            'required' => $fieldDefinition->isRequired,
                            'label' => $label,
                            'multiple' => $fieldDefinition->fieldSettings['isMultiple'],
                            'choices' => array_flip($fieldDefinition->fieldSettings['options']),
                            'choices_as_values' => true,
                        ]
                    )
                    ->addModelTransformer(
                        $fieldDefinition->fieldSettings['isMultiple'] ?
                            new MultiSelectionValueTransformer() :
                            new SingleSelectionValueTransformer()
                    )
                    // Deactivate auto-initialize as we're not on the root form.
                    ->setAutoInitialize(false)
                    ->getForm()
            );
    }
}
