<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\FieldType\Mapper;

use EzSystems\RepositoryForms\Data\Content\FieldData;
use EzSystems\RepositoryForms\FieldType\FieldDefinitionFormMapperInterface;
use EzSystems\RepositoryForms\FieldType\FieldValueFormMapperInterface;
use EzSystems\RepositoryForms\Form\Type\FieldType\SelectionFieldType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SelectionFormMapper implements FieldValueFormMapperInterface
{
    /**
    public function mapFieldValueForm(FormInterface $fieldForm, FieldData $data)
    {
        $fieldDefinition = $data->fieldDefinition;
        $formConfig = $fieldForm->getConfig();
        $languageCode = $fieldForm->getConfig()->getOption('languageCode');

        $choices = $fieldDefinition->fieldSettings['options'];

        if (!empty($fieldDefinition->fieldSettings['multilingualOptions'][$languageCode])) {
            $choices = $fieldDefinition->fieldSettings['multilingualOptions'][$languageCode];
        } elseif (!empty($fieldDefinition->fieldSettings['multilingualOptions'][$fieldDefinition->mainLanguageCode])) {
            $choices = $fieldDefinition->fieldSettings['multilingualOptions'][$fieldDefinition->mainLanguageCode];
        }

        $fieldForm
            ->add(
                $formConfig->getFormFactory()->createBuilder()
                    ->create(
                        'value',
                        SelectionFieldType::class,
                        [
                            'required' => $fieldDefinition->isRequired,
                            'label' => $fieldDefinition->getName(),
                            'multiple' => $fieldDefinition->fieldSettings['isMultiple'],
                            'choices' => array_flip($choices),
                        ]
                    )
                    ->setAutoInitialize(false)
                    ->getForm()
            );
    }
    */
    public function mapFieldValueForm(FormInterface $fieldForm, FieldData $data)
    {
        $fieldDefinition = $data->fieldDefinition;
        $formConfig = $fieldForm->getConfig();
        $languageCode = $fieldForm->getConfig()->getOption('languageCode');

        $choices = $fieldDefinition->fieldSettings['options'];

        if (!empty($fieldDefinition->fieldSettings['multilingualOptions'][$languageCode])) {
            $choices = $fieldDefinition->fieldSettings['multilingualOptions'][$languageCode];
        } elseif (!empty($fieldDefinition->fieldSettings['multilingualOptions'][$fieldDefinition->mainLanguageCode])) {
            $choices = $fieldDefinition->fieldSettings['multilingualOptions'][$fieldDefinition->mainLanguageCode];
        }

        $fieldForm
            ->add(
                $formConfig->getFormFactory()->createBuilder()
                           ->create(
                               'value',
                               SelectionFieldType::class,
                               [
                                   'required' => $fieldDefinition->isRequired,
                                   'label' => $fieldDefinition->getName(),
                                   'multiple' => $fieldDefinition->fieldSettings['isMultiple'],
                                   'choices' => array_flip($choices),
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
