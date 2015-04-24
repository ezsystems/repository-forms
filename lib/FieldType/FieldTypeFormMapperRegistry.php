<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

namespace EzSystems\RepositoryForms\FieldType;

use EzSystems\RepositoryForms\Data\FieldDefinitionData;
use Symfony\Component\Form\FormInterface;

/**
 * Registry for FieldType form mappers.
 */
class FieldTypeFormMapperRegistry implements FieldTypeFormMapperInterface
{
    /**
     * FieldType form mappers, indexed by FieldType identifier.
     *
     * @var FieldTypeFormMapperInterface[]
     */
    private $fieldTypeFormMappers = [];

    /**
     * @return FieldTypeFormMapperInterface[]
     */
    public function getFieldTypeFormMappers()
    {
        return $this->fieldTypeFormMappers;
    }

    /**
     * @param FieldTypeFormMapperInterface $mapper
     * @param string $fieldTypeIdentifier FieldType identifier the mapper is meant for.
     */
    public function addFieldDefFormMapper(FieldTypeFormMapperInterface $mapper, $fieldTypeIdentifier)
    {
        $this->fieldTypeFormMappers[$fieldTypeIdentifier] = $mapper;
    }

    public function mapFieldDefinitionForm(FormInterface $fieldDefinitionForm, FieldDefinitionData $data)
    {
        $fieldTypeIdentifier = $data->getFieldTypeIdentifier();
        if (!isset($this->fieldTypeFormMappers[$fieldTypeIdentifier])) {
            return;
        }

        $this->fieldTypeFormMappers[$fieldTypeIdentifier]->mapFieldDefinitionForm($fieldDefinitionForm, $data);
    }
}
