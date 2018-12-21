<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\FieldType\Mapper;

use eZ\Publish\Core\FieldType\DateAndTime\Type;
use EzSystems\RepositoryForms\Data\Content\FieldData;
use EzSystems\RepositoryForms\Data\FieldDefinitionData;
use EzSystems\RepositoryForms\FieldType\FieldDefinitionFormMapperInterface;
use EzSystems\RepositoryForms\FieldType\FieldValueFormMapperInterface;
use EzSystems\RepositoryForms\Form\Type\DateTimeIntervalType;
use EzSystems\RepositoryForms\Form\Type\FieldType\DateTimeFieldType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * FormMapper for ezdatetime FieldType.
 */
class DateTimeFormMapper implements FieldDefinitionFormMapperInterface, FieldValueFormMapperInterface
{
    public function mapFieldDefinitionForm(FormInterface $fieldDefinitionForm, FieldDefinitionData $data)
    {
        $isTranslation = $data->contentTypeData->languageCode !== $data->contentTypeData->mainLanguageCode;
        $fieldDefinitionForm
            ->add('useSeconds', CheckboxType::class, [
                'required' => false,
                'property_path' => 'fieldSettings[useSeconds]',
                'label' => 'field_definition.ezdatetime.use_seconds',
                'disabled' => $isTranslation,
            ])
            ->add('defaultType', ChoiceType::class, [
                'choices' => [
                    'field_definition.ezdatetime.default_type_empty' => Type::DEFAULT_EMPTY,
                    'field_definition.ezdatetime.default_type_current' => Type::DEFAULT_CURRENT_DATE,
                    'field_definition.ezdatetime.default_type_adjusted' => Type::DEFAULT_CURRENT_DATE_ADJUSTED,
                ],
                'choices_as_values' => true,
                'expanded' => true,
                'required' => true,
                'property_path' => 'fieldSettings[defaultType]',
                'label' => 'field_definition.ezdatetime.default_type',
                'disabled' => $isTranslation,
            ])
            ->add('dateInterval', DateTimeIntervalType::class, [
                'required' => false,
                'property_path' => 'fieldSettings[dateInterval]',
                'label' => 'field_definition.ezdatetime.date_interval',
                'disabled' => $isTranslation,
            ]);
    }

    public function mapFieldValueForm(FormInterface $fieldForm, FieldData $data)
    {
        $fieldDefinition = $data->fieldDefinition;
        $fieldSettings = $fieldDefinition->getFieldSettings();
        $formConfig = $fieldForm->getConfig();

        $fieldForm
            ->add(
                $formConfig->getFormFactory()->createBuilder()
                    ->create(
                        'value',
                        DateTimeFieldType::class,
                        [
                            'with_seconds' => $fieldSettings['useSeconds'],
                            'required' => $fieldDefinition->isRequired,
                            'label' => $fieldDefinition->getName(),
                        ]
                    )
                    ->setAutoInitialize(false)
                    ->getForm()
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'translation_domain' => 'ezrepoforms_content_type',
            ]);
    }
}
