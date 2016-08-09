<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\FieldType\Mapper;

use EzSystems\RepositoryForms\Data\FieldDefinitionData;
use EzSystems\RepositoryForms\FieldType\FieldDefinitionFormMapperInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class IntegerFormMapper implements FieldDefinitionFormMapperInterface
{
    public function mapFieldDefinitionForm(FormInterface $fieldDefinitionForm, FieldDefinitionData $data)
    {
        $fieldDefinitionForm
            ->add(
                'minValue', IntegerType::class, [
                    'required' => false,
                    'property_path' => 'validatorConfiguration[IntegerValueValidator][minIntegerValue]',
                    'label' => 'field_definition.ezinteger.min_value',
                ]
            )
            ->add(
                'maxValue', IntegerType::class, [
                    'required' => false,
                    'property_path' => 'validatorConfiguration[IntegerValueValidator][maxIntegerValue]',
                    'label' => 'field_definition.ezinteger.max_value',
                ]
            );
    }
}
