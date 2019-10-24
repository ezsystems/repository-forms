<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\FieldType;

use EzSystems\RepositoryForms\Data\Content\FieldData;
use Symfony\Component\Form\FormInterface;

/**
 * FieldType mappers dispatcher. Maps Field (definition, value) data to a Form using the appropriate mapper.
 */
interface FieldTypeFormMapperDispatcherInterface
{
    /**
     * Adds a new Field mapper for a fieldtype identifier.
     *
     * @param \EzSystems\RepositoryForms\FieldType\FieldValueFormMapperInterface
     * @param string $fieldTypeIdentifier FieldType identifier this mapper is for.
     *
     * @return mixed
     */
    public function addMapper(FieldValueFormMapperInterface $mapper, string $fieldTypeIdentifier): void;

    /**
     * Maps, if a mapper is available for the fieldtype, $data to $form.
     *
     * @param \Symfony\Component\Form\FormInterface $form
     * @param \EzSystems\RepositoryForms\Data\Content\FieldData $data
     *
     * @throws \InvalidArgumentException If $data is not a FieldData or FieldDefinitionData
     */
    public function map(FormInterface $form, FieldData $data): void;
}
