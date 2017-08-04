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
use EzSystems\RepositoryForms\FieldType\DataTransformer\MultipleCountryValueTransformer;
use EzSystems\RepositoryForms\FieldType\DataTransformer\SingleCountryValueTransformer;
use EzSystems\RepositoryForms\FieldType\FieldDefinitionFormMapperInterface;
use EzSystems\RepositoryForms\FieldType\FieldValueFormMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CountryFormMapper implements FieldDefinitionFormMapperInterface, FieldValueFormMapperInterface
{
    /** @var array Array of countries from ezpublish.fieldType.ezcountry.data */
    protected $countriesInfo;

    /**
     * @param array $countriesInfo
     */
    public function __construct(array $countriesInfo)
    {
        $this->countriesInfo = $countriesInfo;
    }

    public function mapFieldDefinitionForm(FormInterface $fieldDefinitionForm, FieldDefinitionData $data)
    {
        $fieldDefinitionForm
            ->add(
                'isMultiple',
                CheckboxType::class, [
                    'required' => false,
                    'property_path' => 'fieldSettings[isMultiple]',
                    'label' => 'field_definition.ezcountry.is_multiple',
                ]
            )
            ->add(
                // Creating from FormBuilder as we need to add a DataTransformer.
                $fieldDefinitionForm->getConfig()->getFormFactory()->createBuilder()
                    ->create(
                        'defaultValue',
                        ChoiceType::class, [
                            'choices' => $this->getCountryChoices($this->countriesInfo),
                            'choices_as_values' => true,
                            'multiple' => true,
                            'expanded' => false,
                            'required' => false,
                            'label' => 'field_definition.ezcountry.default_value',
                        ]
                    )
                    ->addModelTransformer(new MultipleCountryValueTransformer($this->countriesInfo))
                    // Deactivate auto-initialize as we're not on the root form.
                    ->setAutoInitialize(false)->getForm()
            );
    }

    private function getCountryChoices(array $countriesInfo)
    {
        $choices = [];
        foreach ($countriesInfo as $country) {
            $choices[$country['Name']] = $country['Alpha2'];
        }

        return $choices;
    }

    public function mapFieldValueForm(FormInterface $fieldForm, FieldData $data)
    {
        $fieldDefinition = $data->fieldDefinition;
        $fieldSettings = $fieldDefinition->getFieldSettings();
        $formConfig = $fieldForm->getConfig();

        $fieldForm
            ->add(
                $formConfig->getFormFactory()->createBuilder()
                    ->create('value', ChoiceType::class, [
                        'choices' => $this->getCountryChoices($this->countriesInfo),
                        'multiple' => $fieldSettings['isMultiple'],
                        'expanded' => false,
                        'required' => $fieldDefinition->isRequired,
                        'label' => $fieldDefinition->getName($formConfig->getOption('languageCode')),
                    ])
                    ->addModelTransformer(
                        $fieldSettings['isMultiple']
                            ? new MultipleCountryValueTransformer($this->countriesInfo)
                            : new SingleCountryValueTransformer($this->countriesInfo)
                    )
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
}
