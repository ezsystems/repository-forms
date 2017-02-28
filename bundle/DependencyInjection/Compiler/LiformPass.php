<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace EzSystems\RepositoryFormsBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class LiformPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('liform') || !$container->hasDefinition('ezrepoforms.liform.validation_extension')) {
            return;
        }

        $container->getDefinition('liform')
            ->addMethodCall('addExtension', [new Reference('ezrepoforms.liform.validation_extension')]);

        $resolver = $container->getDefinition('liform.resolver');
        $resolver->addMethodCall('setTransformer', ['file', new Reference('liform.transformer.null')]);
    }
}
