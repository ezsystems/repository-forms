<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Limitation;

@trigger_error(
    sprintf(
        'Interface %s has been deprecated in eZ Platform 3.0 and is going to be removed in 4.0. Please use %s interface instead.',
        LimitationValueMapperInterface::class,
        \EzSystems\EzPlatformAdminUi\Limitation\LimitationValueMapperInterface::class
    ),
    E_DEPRECATED
);

if (!class_exists(\EzSystems\EzPlatformAdminUi\Limitation\LimitationValueMapperInterface::class)) {
    /**
     * @deprecated Interface LimitationValueMapperInterface has been deprecated in eZ Platform 3.0
     *             and is going to be removed in 4.0. Please use
     *             \EzSystems\EzPlatformAdminUi\Limitation\LimitationValueMapperInterface interface instead.
     */
    interface LimitationValueMapperInterface
    {
    }
}
