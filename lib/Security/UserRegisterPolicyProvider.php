<?php
/**
 * Created by PhpStorm.
 * User: bdunogier
 * Date: 03/06/2016
 * Time: 18:03
 */

namespace EzSystems\RepositoryForms\Security;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\ConfigBuilderInterface;
use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Security\PolicyProvider\PolicyProviderInterface;

class UserRegisterPolicyProvider implements PolicyProviderInterface
{
    public function addPolicies(ConfigBuilderInterface $configBuilder)
    {
        $configBuilder->addConfig([
            "user" => [
                "register" => null,
            ],
        ]);
    }
}
