<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\FieldType;

@trigger_error(
    sprintf(
        'Interface %s has been deprecated in eZ Platform 3.0 and is going to be removed in 4.0. Please use %s interface instead.',
        FieldValueFormMapperInterface::class,
        \EzSystems\EzPlatformContentForms\FieldType\FieldValueFormMapperInterface::class
    ),
    E_DEPRECATED
);

if (!class_exists(\EzSystems\EzPlatformContentForms\FieldType\FieldValueFormMapperInterface::class)) {
    /**
     * @deprecated Interface FieldValueFormMapperInterface has been deprecated in eZ Platform 3.0
     *             and is going to be removed in 4.0. Please use
     *             \EzSystems\EzPlatformContentForms\FieldType\FieldValueFormMapperInterface interface instead.
     */
    interface FieldValueFormMapperInterface
    {
    }
}
