<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 *
 * @version //autogentag//
 */
namespace EzSystems\RepositoryForms\FieldType;

use EzSystems\RepositoryForms\Data\FieldDefinitionData;
use Symfony\Component\Form\FormInterface;
use InvalidArgumentException;

/**
 * Registry for FieldType form mappers.
 */
class FieldTypeFormMapperRegistry implements FieldTypeFormMapperRegistryInterface
{
    /**
     * FieldType form mappers, indexed by FieldType identifier.
     *
     * @var FieldTypeFormMapperInterface[]
     */
    private $fieldTypeFormMappers = [];

    public function getMappers()
    {
        return $this->fieldTypeFormMappers;
    }

    public function addMapper(FieldTypeFormMapperInterface $mapper, $fieldTypeIdentifier)
    {
        $this->fieldTypeFormMappers[$fieldTypeIdentifier] = $mapper;
    }

    public function mapFieldDefinitionForm(FormInterface $fieldDefinitionForm, FieldDefinitionData $data)
    {
        $fieldTypeIdentifier = $data->getFieldTypeIdentifier();
        if (!$this->hasMapper($fieldTypeIdentifier)) {
            return;
        }

        $this->fieldTypeFormMappers[$fieldTypeIdentifier]->mapFieldDefinitionForm($fieldDefinitionForm, $data);
    }

    /**
     * Returns mapper corresponding to given FieldType identifier.
     *
     * @throws \InvalidArgumentException If no mapper exists for $fieldTypeIdentifier.
     *
     * @return FieldTypeFormMapperInterface
     */
    public function getMapper($fieldTypeIdentifier)
    {
        if (!$this->hasMapper($fieldTypeIdentifier)) {
            throw new InvalidArgumentException("No FieldTypeFormMapper found for '$fieldTypeIdentifier'");
        }

        return $this->fieldTypeFormMappers[$fieldTypeIdentifier];
    }

    /**
     * Checks if a mapper exists for given FieldType identifier.
     *
     * @param string $fieldTypeIdentifier
     *
     * @return bool
     */
    public function hasMapper($fieldTypeIdentifier)
    {
        return isset($this->fieldTypeFormMappers[$fieldTypeIdentifier]);
    }
}
