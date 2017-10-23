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
use EzSystems\RepositoryForms\FieldType\FieldDefinitionFormMapperInterface;
use EzSystems\RepositoryForms\FieldType\FieldValueFormMapperInterface;
use EzSystems\RepositoryForms\Form\Type\FieldType\ISBNFieldType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ISBNFormMapper implements FieldDefinitionFormMapperInterface, FieldValueFormMapperInterface
{
    public function mapFieldDefinitionForm(FormInterface $fieldDefinitionForm, FieldDefinitionData $fieldDefinition)
    {
        $defaultValueForm = $fieldDefinitionForm
            ->getConfig()
            ->getFormFactory()
            ->createBuilder()
            ->create('defaultValue', ISBNFieldType::class, [
                'required' => false,
                'label' => 'field_definition.ezisbn.default_value',
            ])
            ->setAutoInitialize(false)
            ->getForm();

        $fieldDefinitionForm
            ->add(
                'isISBN13', CheckboxType::class, [
                    'required' => false,
                    'property_path' => 'fieldSettings[isISBN13]',
                    'label' => 'field_definition.ezisbn.is_isbn13',
                ]
            )
            ->add($defaultValueForm);
    }

    public function mapFieldValueForm(FormInterface $fieldForm, FieldData $data)
    {
        $fieldDefinition = $data->fieldDefinition;
        $formConfig = $fieldForm->getConfig();

        $fieldForm
            ->add(
                $formConfig->getFormFactory()->createBuilder()
                    ->create(
                        'value',
                        ISBNFieldType::class,
                        [
                            'required' => $fieldDefinition->isRequired,
                            'label' => $fieldDefinition->getName($formConfig->getOption('languageCode')),
                        ]
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
