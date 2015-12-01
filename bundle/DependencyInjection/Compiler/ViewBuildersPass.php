<?php
/**
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryFormsBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ViewBuildersPass implements CompilerPassInterface
{
    /**
     * Registers the view builders into the view builder registry.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('ezpublish.view_builder.registry')) {
            return;
        }

        $viewBuilderRegistry = $container->findDefinition('ezpublish.view_builder.registry');
        $viewBuilders = [
            $container->findDefinition('ezrepoforms.view.content_edit.builder'),
        ];

        $viewBuilderRegistry->addMethodCall('addToRegistry', [$viewBuilders]);
    }
}
