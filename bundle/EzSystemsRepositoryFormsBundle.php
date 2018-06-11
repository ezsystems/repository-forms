<?php

/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryFormsBundle;

use EzSystems\RepositoryFormsBundle\DependencyInjection\Compiler\FieldTypeFormMapperDispatcherPass;
use EzSystems\RepositoryFormsBundle\DependencyInjection\Compiler\LimitationFormMapperPass;
use EzSystems\RepositoryFormsBundle\DependencyInjection\Compiler\LimitationValueMapperPass;
use EzSystems\RepositoryFormsBundle\DependencyInjection\Compiler\ViewBuilderRegistryPass;
use EzSystems\RepositoryFormsBundle\DependencyInjection\Configuration\Parser\ContentCreateView;
use EzSystems\RepositoryFormsBundle\DependencyInjection\Configuration\Parser\ContentEdit;
use EzSystems\RepositoryFormsBundle\DependencyInjection\Configuration\Parser\ContentEditView;
use EzSystems\RepositoryFormsBundle\DependencyInjection\Configuration\Parser\LimitationValueTemplates;
use EzSystems\RepositoryFormsBundle\DependencyInjection\Configuration\Parser\UserEdit;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class EzSystemsRepositoryFormsBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new FieldTypeFormMapperDispatcherPass());
        $container->addCompilerPass(new LimitationFormMapperPass());
        $container->addCompilerPass(new LimitationValueMapperPass());
        $container->addCompilerPass(new ViewBuilderRegistryPass());

        $eZExtension = $container->getExtension('ezpublish');
        $eZExtension->addConfigParser(new ContentEdit());
        $eZExtension->addConfigParser(new UserEdit());
        $eZExtension->addConfigParser(new LimitationValueTemplates());
        $eZExtension->addConfigParser(new ContentEditView());
        $eZExtension->addConfigParser(new ContentCreateView());
        $eZExtension->addDefaultSettings(__DIR__ . '/Resources/config', ['ezpublish_default_settings.yml']);
    }
}
