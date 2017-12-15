<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\UserRegister;

use eZ\Publish\API\Repository\Values\User\UserGroup;

/**
 * Used to load a user group during registration.
 */
interface RegistrationGroupLoader
{
    /**
     * Loads a parent group.
     *
     * @return UserGroup
     */
    public function loadGroup();
}
