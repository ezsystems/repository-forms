<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Data;

@trigger_error(
    sprintf(
        'Class %s has been deprecated in eZ Platform 3.0 and is going to be removed in 4.0. Please use %s class instead.',
        FieldDefinitionData::class,
        \EzSystems\EzPlatformAdminUi\Form\Data\FieldDefinitionData::class
    ),
    E_DEPRECATED
);

if (!class_exists(\EzSystems\EzPlatformAdminUi\Form\Data\FieldDefinitionData::class)) {
    /**
     * @deprecated Class FieldDefinitionData has been deprecated in eZ Platform 3.0
     *             and is going to be removed in 4.0. Please use
     *             \EzSystems\EzPlatformAdminUi\Form\Data\FieldDefinitionData class instead.
     */
    class FieldDefinitionData
    {
    }
}
