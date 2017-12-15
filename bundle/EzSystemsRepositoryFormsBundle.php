<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryFormsBundle;

use EzSystems\RepositoryForms\Security\UserRegisterPolicyProvider;
use EzSystems\RepositoryFormsBundle\DependencyInjection\Compiler\FieldTypeFormMapperDispatcherPass;
use EzSystems\RepositoryFormsBundle\DependencyInjection\Compiler\FieldTypeFormMapperPass;
use EzSystems\RepositoryFormsBundle\DependencyInjection\Compiler\LimitationFormMapperPass;
use EzSystems\RepositoryFormsBundle\DependencyInjection\Compiler\LimitationValueMapperPass;
use EzSystems\RepositoryFormsBundle\DependencyInjection\Configuration\Parser\ContentEdit;
use EzSystems\RepositoryFormsBundle\DependencyInjection\Configuration\Parser\LimitationValueTemplates;
use EzSystems\RepositoryFormsBundle\DependencyInjection\Configuration\Parser\UserEdit;
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
        $container->addCompilerPass(new LimitationValueMapperPass());

        $eZExtension = $container->getExtension('ezpublish');
        $eZExtension->addPolicyProvider(new UserRegisterPolicyProvider());
        $eZExtension->addConfigParser(new UserRegistration());
        $eZExtension->addConfigParser(new ContentEdit());
        $eZExtension->addConfigParser(new UserEdit());
        $eZExtension->addConfigParser(new LimitationValueTemplates());
        $eZExtension->addDefaultSettings(__DIR__ . '/Resources/config', ['ezpublish_default_settings.yml']);
    }
}
