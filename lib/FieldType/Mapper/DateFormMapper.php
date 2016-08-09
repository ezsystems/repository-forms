<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\FieldType\Mapper;

use eZ\Publish\Core\FieldType\Date\Type;
use EzSystems\RepositoryForms\Data\FieldDefinitionData;
use EzSystems\RepositoryForms\FieldType\FieldDefinitionFormMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;

class DateFormMapper implements FieldDefinitionFormMapperInterface
{
    public function mapFieldDefinitionForm(FormInterface $fieldDefinitionForm, FieldDefinitionData $data)
    {
        $fieldDefinitionForm
            ->add(
                'defaultType',
                ChoiceType::class,
                [
                    'choices' => [
                        Type::DEFAULT_EMPTY => 'field_definition.ezdate.default_type_empty',
                        Type::DEFAULT_CURRENT_DATE => 'field_definition.ezdate.default_type_current',
                    ],
                    'expanded' => true,
                    'required' => true,
                    'property_path' => 'fieldSettings[defaultType]',
                    'label' => 'field_definition.ezdate.default_type',
                ]
            );
    }
}
