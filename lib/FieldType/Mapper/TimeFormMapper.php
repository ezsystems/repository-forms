<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\FieldType\Mapper;

use eZ\Publish\Core\FieldType\Time\Type;
use EzSystems\RepositoryForms\Data\FieldDefinitionData;
use EzSystems\RepositoryForms\FieldType\FieldDefinitionFormMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;

class TimeFormMapper implements FieldDefinitionFormMapperInterface
{
    public function mapFieldDefinitionForm(FormInterface $fieldDefinitionForm, FieldDefinitionData $data)
    {
        $fieldDefinitionForm
            ->add(
                'useSeconds',
                CheckboxType::class,
                [
                    'required' => false,
                    'property_path' => 'fieldSettings[useSeconds]',
                    'label' => 'field_definition.eztime.use_seconds',
                ]
            )
            ->add(
                'defaultType',
                ChoiceType::class,
                [
                    'choices' => [
                        'field_definition.eztime.default_type_empty' => Type::DEFAULT_EMPTY,
                        'field_definition.eztime.default_type_current' => Type::DEFAULT_CURRENT_TIME,
                    ],
                    'choices_as_values' => true,
                    'expanded' => true,
                    'required' => true,
                    'property_path' => 'fieldSettings[defaultType]',
                    'label' => 'field_definition.eztime.default_type',
                ]
            );
    }
}
