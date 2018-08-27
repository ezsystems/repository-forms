<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\FieldType\Mapper;

use eZ\Publish\Core\FieldType\Author\Type;
use EzSystems\RepositoryForms\Data\Content\FieldData;
use EzSystems\RepositoryForms\Data\FieldDefinitionData;
use EzSystems\RepositoryForms\FieldType\FieldDefinitionFormMapperInterface;
use EzSystems\RepositoryForms\FieldType\FieldValueFormMapperInterface;
use EzSystems\RepositoryForms\Form\Type\FieldType\AuthorFieldType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * FormMapper for ezauthor FieldType.
 */
class AuthorFormMapper implements FieldDefinitionFormMapperInterface, FieldValueFormMapperInterface
{
    /**
     * @param \Symfony\Component\Form\FormInterface $fieldDefinitionForm
     * @param \EzSystems\RepositoryForms\Data\FieldDefinitionData $data
     */
    public function mapFieldDefinitionForm(FormInterface $fieldDefinitionForm, FieldDefinitionData $data)
    {
        $fieldDefinitionForm
            ->add(
                'defaultAuthor',
                ChoiceType::class,
                [
                    'choices' => [
                        'field_definition.ezauthor.default_user_current' => Type::DEFAULT_CURRENT_USER,
                        'field_definition.ezauthor.default_user_empty' => Type::DEFAULT_EMPTY,
                    ],
                    'choices_as_values' => true,
                    'expanded' => true,
                    'required' => true,
                    'property_path' => 'fieldSettings[defaultAuthor]',
                    'label' => 'field_definition.ezauthor.default_author',
                    'translation_domain' => 'ezrepoforms_content_type',
                ]
            );
    }

    /**
     * @param \Symfony\Component\Form\FormInterface $fieldForm
     * @param \EzSystems\RepositoryForms\Data\Content\FieldData $data
     */
    public function mapFieldValueForm(FormInterface $fieldForm, FieldData $data)
    {
        $fieldDefinition = $data->fieldDefinition;
        $fieldSettings = $fieldDefinition->getFieldSettings();
        $formConfig = $fieldForm->getConfig();
        $names = $fieldDefinition->getNames();
        $label = $fieldDefinition->getName($formConfig->getOption('mainLanguageCode')) ?: reset($names);

        $fieldForm
            ->add(
                $formConfig->getFormFactory()->createBuilder()
                    ->create('value', AuthorFieldType::class, [
                        'default_author' => $fieldSettings['defaultAuthor'],
                        'required' => $fieldDefinition->isRequired,
                        'label' => $label,
                    ])
                    ->setAutoInitialize(false)
                    ->getForm()
            );
    }

    /**
     * Fake method to set the translation domain for the extractor.
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'translation_domain' => 'ezrepoforms_content_type',
            ]);
    }
}
