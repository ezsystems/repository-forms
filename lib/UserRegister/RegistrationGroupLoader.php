<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */
namespace EzSystems\RepositoryForms\UserRegister;

use eZ\Publish\API\Repository\Values\User\UserGroup;
use EzSystems\RepositoryForms\Data\User\UserCreateData;

/**
 * Used to set the parent group during user registration.
 */
interface RegistrationGroupLoader
{
    /**
     * Gets the parent group of $userCreateData.
     *
     * @param UserCreateData $userCreateData
     *
     * @return UserGroup
     */
    public function getParentGroup(UserCreateData $userCreateData);
}
