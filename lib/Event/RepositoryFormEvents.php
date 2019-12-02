<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Event;

use EzSystems\EzPlatformAdminUi\Event\FormEvents;
use EzSystems\EzPlatformContentForms\Event\ContentFormEvents;
use EzSystems\EzPlatformUser\Form\UserFormEvents;

@trigger_error(
    sprintf(
        'Class %s has been deprecated in eZ Platform 3.0 and is going to be removed in 4.0. Please use %s, %s, %s classes instead.',
        RepositoryFormEvents::class,
        FormEvents::class,
        ContentFormEvents::class,
        UserFormEvents::class
    ),
    E_DEPRECATED
);

final class RepositoryFormEvents
{
    /**
     * Base name for ContentType update processing events.
     */
    public const CONTENT_TYPE_UPDATE = FormEvents::CONTENT_TYPE_UPDATE;

    /**
     * Triggered when adding a FieldDefinition to the ContentTypeDraft.
     */
    public const CONTENT_TYPE_ADD_FIELD_DEFINITION = FormEvents::CONTENT_TYPE_ADD_FIELD_DEFINITION;

    /**
     * Triggered when removing a FieldDefinition from the ContentTypeDraft.
     */
    public const CONTENT_TYPE_REMOVE_FIELD_DEFINITION = FormEvents::CONTENT_TYPE_REMOVE_FIELD_DEFINITION;

    /**
     * Triggered when saving the draft + publishing the ContentType.
     */
    public const CONTENT_TYPE_PUBLISH = FormEvents::CONTENT_TYPE_PUBLISH;

    /**
     * Triggered when removing the draft (e.g. "cancel" action).
     */
    public const CONTENT_TYPE_REMOVE_DRAFT = FormEvents::CONTENT_TYPE_REMOVE_DRAFT;

    /**
     * Base name for Content edit processing events.
     */
    public const CONTENT_EDIT = ContentFormEvents::CONTENT_EDIT;

    /**
     * Triggered when saving a content draft.
     */
    public const CONTENT_SAVE_DRAFT = ContentFormEvents::CONTENT_SAVE_DRAFT;

    /**
     * Triggered when creating a content draft.
     */
    public const CONTENT_CREATE_DRAFT = ContentFormEvents::CONTENT_CREATE_DRAFT;

    /**
     * Triggered when publishing a content.
     */
    public const CONTENT_PUBLISH = ContentFormEvents::CONTENT_PUBLISH;

    /**
     * Triggered when canceling a content edition.
     */
    public const CONTENT_CANCEL = ContentFormEvents::CONTENT_CANCEL;

    /**
     * Base name for User edit processing events.
     */
    public const USER_EDIT = ContentFormEvents::USER_EDIT;

    /**
     * Triggered when saving an user.
     */
    public const USER_UPDATE = ContentFormEvents::USER_UPDATE;

    /**
     * Triggered when creating an user.
     */
    public const USER_CREATE = ContentFormEvents::USER_CREATE;

    /**
     * Triggered when registering an user.
     */
    public const USER_REGISTER = UserFormEvents::USER_REGISTER;

    /**
     * Triggered when canceling a user edition.
     */
    public const USER_CANCEL = ContentFormEvents::USER_CANCEL;
}
