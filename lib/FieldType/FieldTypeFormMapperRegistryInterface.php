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

/**
 * Interface for FieldType form mappers registry.
 */
interface FieldTypeFormMapperRegistryInterface
{
    /**
     * @return FieldTypeFormMapperInterface[]
     */
    public function getMappers();

    /**
     * Returns mapper corresponding to given FieldType identifier.
     *
     * @throws \InvalidArgumentException If no mapper exists for $fieldTypeIdentifier.
     *
     * @return FieldTypeFormMapperInterface
     */
    public function getMapper($fieldTypeIdentifier);

    /**
     * Checks if a mapper exists for given FieldType identifier.
     *
     * @param string $fieldTypeIdentifier
     *
     * @return bool
     */
    public function hasMapper($fieldTypeIdentifier);

    /**
     * @param FieldTypeFormMapperInterface $mapper
     * @param string $fieldTypeIdentifier FieldType identifier the mapper is meant for.
     */
    public function addMapper(FieldTypeFormMapperInterface $mapper, $fieldTypeIdentifier);
}
