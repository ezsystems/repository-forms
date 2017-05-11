<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 *
 * @version //autogentag//
 */
namespace EzSystems\RepositoryFormsBundle;

use EzSystems\RepositoryForms\Security\UserRegisterPolicyProvider;
use EzSystems\RepositoryFormsBundle\DependencyInjection\Compiler\FieldTypeFormMapperDispatcherPass;
use EzSystems\RepositoryFormsBundle\DependencyInjection\Compiler\FieldTypeFormMapperPass;
use EzSystems\RepositoryFormsBundle\DependencyInjection\Compiler\LiformPass;
use EzSystems\RepositoryFormsBundle\DependencyInjection\Compiler\LimitationFormMapperPass;
use EzSystems\RepositoryFormsBundle\DependencyInjection\Configuration\Parser\UserRegistration;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class EzSystemsRepositoryFormsBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new FieldTypeFormMapperPass());
        $container->addCompilerPass(new FieldTypeFormMapperDispatcherPass());
        $container->addCompilerPass(new LimitationFormMapperPass());
        $container->addCompilerPass(new LiformPass());

        $eZExtension = $container->getExtension('ezpublish');
        $eZExtension->addPolicyProvider(new UserRegisterPolicyProvider());
        $eZExtension->addConfigParser(new UserRegistration());
        $eZExtension->addDefaultSettings(__DIR__ . '/Resources/config', ['ezpublish_default_settings.yml']);
    }
}
