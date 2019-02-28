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
use EzSystems\RepositoryForms\Form\Type\FieldType\RelationListFieldType;
use EzSystems\RepositoryForms\Form\Type\LocationType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RelationListFormMapper extends AbstractRelationFormMapper
{
    public function mapFieldDefinitionForm(FormInterface $fieldDefinitionForm, FieldDefinitionData $data)
    {
        $isTranslation = $data->contentTypeData->languageCode !== $data->contentTypeData->mainLanguageCode;
        $fieldDefinitionForm
            ->add('selectionDefaultLocation', LocationType::class, [
                'required' => false,
                'property_path' => 'fieldSettings[selectionDefaultLocation]',
                'label' => 'field_definition.ezobjectrelationlist.selection_default_location',
                'disabled' => $isTranslation,
            ])
            ->add('selectionContentTypes', ChoiceType::class, [
                'choices' => $this->getContentTypesHash(),
                'choices_as_values' => true,
                'expanded' => false,
                'multiple' => true,
                'required' => false,
                'property_path' => 'fieldSettings[selectionContentTypes]',
                'label' => 'field_definition.ezobjectrelationlist.selection_content_types',
                'disabled' => $isTranslation,
            ])
            ->add('selectionLimit', IntegerType::class, [
                'required' => false,
                'empty_data' => 0,
                'property_path' => 'validatorConfiguration[RelationListValueValidator][selectionLimit]',
                'label' => /** @Desc("Selection limit") */ 'field_definition.ezobjectrelationlist.selection_limit',
                'disabled' => $isTranslation,
            ]);
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
                        RelationListFieldType::class,
                        [
                            'required' => $fieldDefinition->isRequired,
                            'label' => $fieldDefinition->getName(),
                            'default_location' => $this->loadDefaultLocationForSelection(
                                $fieldDefinition->getFieldSettings()['selectionDefaultLocation']
                            ),
                        ]
                    )
                    ->setAutoInitialize(false)
                    ->getForm()
            );
    }

    /**
     * Fake method to set the translation domain for the extractor.
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
