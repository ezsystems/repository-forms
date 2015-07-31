<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 *
 * @version //autogentag//
 */
namespace EzSystems\RepositoryForms\FieldType\Mapper;

use eZ\Publish\Core\FieldType\DateAndTime\Type;
use EzSystems\RepositoryForms\Data\FieldDefinitionData;
use EzSystems\RepositoryForms\FieldType\FieldTypeFormMapperInterface;
use EzSystems\RepositoryForms\Form\Type\DateTimeIntervalType;
use Symfony\Component\Form\FormInterface;

class DateTimeFormMapper implements FieldTypeFormMapperInterface
{
    public function mapFieldDefinitionForm(FormInterface $fieldDefinitionForm, FieldDefinitionData $data)
    {
        $fieldDefinitionForm
            ->add('useSeconds', 'checkbox', [
                'required' => false,
                'property_path' => 'fieldSettings[useSeconds]',
                'label' => 'field_definition.ezdatetime.use_seconds',
            ])
            ->add('defaultType', 'choice', [
                'choices' => [
                    Type::DEFAULT_EMPTY => 'field_definition.ezdatetime.default_type_empty',
                    Type::DEFAULT_CURRENT_DATE => 'field_definition.ezdatetime.default_type_current',
                    Type::DEFAULT_CURRENT_DATE_ADJUSTED => 'field_definition.ezdatetime.default_type_adjusted',
                ],
                'expanded' => true,
                'required' => true,
                'property_path' => 'fieldSettings[defaultType]',
                'label' => 'field_definition.ezdatetime.default_type',
            ])
            ->add('dateInterval', new DateTimeIntervalType(), [
                'required' => false,
                'property_path' => 'fieldSettings[dateInterval]',
                'label' => 'field_definition.ezdatetime.date_interval',
            ]);
    }
}
