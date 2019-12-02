<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Event;

@trigger_error(
    sprintf(
        'Class %s has been deprecated in eZ Platform 3.0 and is going to be removed in 4.0. Please use %s class instead.',
        FormActionEvent::class,
        \EzSystems\EzPlatformContentForms\Event\FormActionEvent::class
    ),
    E_DEPRECATED
);

if (!class_exists(\EzSystems\EzPlatformContentForms\Event\FormActionEvent::class)) {
    /**
     * @deprecated Class FormActionEvent has been deprecated in eZ Platform 3.0
     *             and is going to be removed in 4.0. Please use
     *             \EzSystems\EzPlatformContentForms\Event\FormActionEvent class instead.
     */
    class FormActionEvent
    {
    }
}
