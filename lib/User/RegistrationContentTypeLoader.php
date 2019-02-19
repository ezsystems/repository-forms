<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\User;

use EzSystems\EzPlatformUser\ConfigResolver\RegistrationContentTypeLoader as BaseRegistrationContentTypeLoader;

/**
 * Loads the content type used by user registration.
 * @deprecated Deprecated in 2.5 and will be removed in 3.0. Please use \EzSystems\EzPlatformUser\ConfigResolver\RegistrationContentTypeLoader instead.
 */
interface RegistrationContentTypeLoader extends BaseRegistrationContentTypeLoader
{
}
