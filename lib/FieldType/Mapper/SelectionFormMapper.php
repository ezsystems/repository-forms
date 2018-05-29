<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\FieldType\Mapper;

use EzSystems\RepositoryForms\Data\Content\FieldData;
use EzSystems\RepositoryForms\Data\FieldDefinitionData;
use EzSystems\RepositoryForms\FieldType\DataTransformer\MultiSelectionValueTransformer;
use EzSystems\RepositoryForms\FieldType\DataTransformer\SingleSelectionValueTransformer;
use EzSystems\RepositoryForms\FieldType\FieldDefinitionFormMapperInterface;
use EzSystems\RepositoryForms\FieldType\FieldValueFormMapperInterface;
use EzSystems\RepositoryForms\Form\DataTransformer\SelectionOptionDefaultValueTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class SelectionFormMapper implements FieldDefinitionFormMapperInterface, FieldValueFormMapperInterface
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
        $this->prepareDefaultValueForForm($data);

        $formConfig = $fieldDefinitionForm->getConfig();

        $fieldDefinitionForm
            ->add('isMultiple', CheckboxType::class, [
                'required' => false,
                'property_path' => 'fieldSettings[isMultiple]',
                'label' => 'field_definition.ezselection.is_multiple',
            ])
            ->add('options', CollectionType::class, [
                'entry_type' => TextType::class,
                'entry_options' => ['required' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'delete_empty' => false,
                'prototype' => true,
                'prototype_name' => '__number__',
                'required' => false,
                'property_path' => 'fieldSettings[options]',
                'label' => 'field_definition.ezselection.options',
            ])
            ->add(
                $formConfig->getFormFactory()->createBuilder()
                    ->create('defaults', CollectionType::class, [
                        'entry_type' => CheckboxType::class,
                        'entry_options' => [
                            'required' => false,
                            'attr' => ['class' => 'ezselection-option-is-default-checkbox'],
                        ],
                        'allow_add' => true,
                        'allow_delete' => true,
                        'delete_empty' => false,
                        'prototype' => true,
                        'prototype_name' => '__number__',
                        'prototype_data' => false,
                        'required' => false,
                        'property_path' => 'fieldSettings[defaultValue]',
                        'label' => 'field_definition.ezselection.defaults',
                    ])
                    ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
                        // Adds empty checkboxes to form, so when validation fails ir still contains checkboxes for all options.
                        $this->prepareDefaultValueCheckboxes($event->getForm());
                    })
                    ->addModelTransformer(new SelectionOptionDefaultValueTransformer($data->fieldSettings['options']))
                    ->setAutoInitialize(false)
                    ->getForm()
            );
    }

    public function mapFieldValueForm(FormInterface $fieldForm, FieldData $data)
    {
        $fieldDefinition = $data->fieldDefinition;
        $formConfig = $fieldForm->getConfig();
        $names = $fieldDefinition->getNames();
        $label = $fieldDefinition->getName($formConfig->getOption('languageCode')) ?: reset($names);

        $fieldForm
            ->add(
                $formConfig->getFormFactory()->createBuilder()
                    ->create(
                        'value',
                        ChoiceType::class,
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

    /**
     * Fake method to set the translation domain for the extractor.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'translation_domain' => 'ezrepoforms_content_type',
            ]);
    }

    /**
     * Manipulates form data so number of checkboxes match number of options.
     * @param FormInterface $form
     */
    private function prepareDefaultValueCheckboxes(FormInterface $form)
    {
        $parentForm = $form->getParent();
        $options = $parentForm->get('options')->getData();
        $defaultValues = [];

        foreach ($options as $option) {
            $defaultValues[] = false;
        }

        $form->setData($defaultValues);
    }

    /**
     * Converts and set fieldSetting defaultValue to an array of booleans so it can be passed to form.
     * @param FieldDefinitionData $data
     */
    private function prepareDefaultValueForForm(FieldDefinitionData $data)
    {
        $defaultValues = [];

        foreach ($data->fieldSettings['options'] as $key => $option) {
            $defaultValues[$key] = in_array($key, $data->fieldSettings['defaultValue']);
        }

        $data->fieldSettings['defaultValue'] = $defaultValues;
    }
}
