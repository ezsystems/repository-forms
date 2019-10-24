<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Event;

final class ContentFormEvents
{
    /**
     * Base name for Content edit processing events.
     */
    const CONTENT_EDIT = 'content.edit';

    /**
     * Triggered when saving a content draft.
     */
    const CONTENT_SAVE_DRAFT = 'content.edit.saveDraft';

    /**
     * Triggered when creating a content draft.
     */
    const CONTENT_CREATE_DRAFT = 'content.edit.createDraft';

    /**
     * Triggered when publishing a content.
     */
    const CONTENT_PUBLISH = 'content.edit.publish';

    /**
     * Triggered when canceling a content edition.
     */
    const CONTENT_CANCEL = 'content.edit.cancel';

    /**
     * Base name for User edit processing events.
     */
    const USER_EDIT = 'user.edit';

    /**
     * Triggered when saving an user.
     */
    const USER_UPDATE = 'user.edit.update';

    /**
     * Triggered when creating an user.
     */
    const USER_CREATE = 'user.edit.create';

    /**
     * Triggered when canceling a user edition.
     */
    const USER_CANCEL = 'user.edit.cancel';
}
