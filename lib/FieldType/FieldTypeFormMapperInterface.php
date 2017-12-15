<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\FieldType;

/**
 * Interface for FieldType form mappers.
 *
 * It maps a FieldType's specifics to editing Forms (e.g. FieldDefinition forms).
 *
 * @deprecated since 1.1, will be removed in 2.0. Use FieldDefinitionFormMapperInterface instead.
 */
interface FieldTypeFormMapperInterface extends FieldDefinitionFormMapperInterface
{
}
