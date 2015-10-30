<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 *
 * @version //autogentag//
 */
namespace EzSystems\RepositoryForms\Event;

final class RepositoryFormEvents
{
    /**
     * Base name for ContentType update processing events.
     */
    const CONTENT_TYPE_UPDATE = 'contentType.update';

    /**
     * Triggered when adding a FieldDefinition to the ContentTypeDraft.
     */
    const CONTENT_TYPE_ADD_FIELD_DEFINITION = 'contentType.update.addFieldDefinition';

    /**
     * Triggered when removing a FieldDefinition from the ContentTypeDraft.
     */
    const CONTENT_TYPE_REMOVE_FIELD_DEFINITION = 'contentType.update.removeFieldDefinition';

    /**
     * Triggered when saving the draft + publishing the ContentType.
     */
    const CONTENT_TYPE_PUBLISH = 'contentType.update.publishContentType';

    /**
     * Triggered when removing the draft (e.g. "cancel" action).
     */
    const CONTENT_TYPE_REMOVE_DRAFT = 'contentType.update.removeDraft';

    /**
     * Triggered when updating a ContentType group.
     */
    const CONTENT_TYPE_GROUP_UPDATE = 'contentType.group.update';

    /**
     * Base name for Role update processing events.
     */
    const ROLE_UPDATE = 'role.update';

    /**
     * Triggered when saving the role.
     */
    const ROLE_SAVE = 'role.update.saveRole';

    /**
     * Triggered when removing the draft (e.g. "cancel" action).
     */
    const ROLE_REMOVE_DRAFT = 'role.update.removeDraft';

    /**
     * Base name for Policy update processing events.
     */
    const POLICY_UPDATE = 'policy.update';

    /**
     * Triggered when saving the policy.
     */
    const POLICY_SAVE = 'policy.update.savePolicy';

    /**
     * Triggered when canceling policy edition.
     */
    const POLICY_REMOVE_DRAFT = 'policy.update.removeDraft';

    /**
     * Triggered when updating a section.
     */
    const SECTION_UPDATE = 'section.update';

    /**
     * Triggered when updating a language.
     */
    const LANGUAGE_UPDATE = 'language.update';
}
