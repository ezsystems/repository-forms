<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryForms\Security;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\ConfigBuilderInterface;
use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Security\PolicyProvider\PolicyProviderInterface;

/**
 * @deprecated Deprecated since 2.1. No longer used. This policy was moved to the eZ/Publish/Core/settings/policies.yml.
 *
 * Adds the user/register policy.
 */
class UserRegisterPolicyProvider implements PolicyProviderInterface
{
    public function addPolicies(ConfigBuilderInterface $configBuilder)
    {
        @trigger_error('Method ' . __METHOD__ . ' is deprecated since 2.1', E_USER_DEPRECATED);
    }
}
