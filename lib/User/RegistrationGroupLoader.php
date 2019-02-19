<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\User;

use EzSystems\EzPlatformUser\ConfigResolver\RegistrationGroupLoader as BaseRegistrationGroupLoader;

/**
 * Used to load a user group during registration.
 * @deprecated Deprecated in 1.5 and will be removed in 2.0. Please use \EzSystems\EzPlatformUser\ConfigResolver\RegistrationGroupLoader instead.
 */
interface RegistrationGroupLoader extends BaseRegistrationGroupLoader
{
}
