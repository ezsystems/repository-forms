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
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormInterface;

class RelationFormMapper implements FieldDefinitionFormMapperInterface
{
    public function mapFieldDefinitionForm(FormInterface $fieldDefinitionForm, FieldDefinitionData $data)
    {
        $fieldDefinitionForm
            ->add('selectionRoot', HiddenType::class, [
                'required' => false,
                'property_path' => 'fieldSettings[selectionRoot]',
                'label' => 'field_definition.ezobjectrelation.selection_root',
            ]);
    }
}
