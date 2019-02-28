<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Form\Type\User;

use EzSystems\EzPlatformUser\Form\Type\UserRegisterType as BaseUserRegisterType;

/**
 * Form type for content edition (create/update).
 * Underlying data will be either \EzSystems\RepositoryForms\Data\Content\ContentCreateData or \EzSystems\RepositoryForms\Data\Content\ContentUpdateData
 * depending on the context (create or update).
 * @deprecated Deprecated in 2.5 and will be removed in 3.0. Please use \EzSystems\EzPlatformUser\Form\Type\UserRegisterType instead.
 */
class UserRegisterType extends BaseUserRegisterType
{
}
