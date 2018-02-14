<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryFormsBundle\DependencyInjection\Compiler;

use EzSystems\RepositoryForms\Content\View\Builder\ContentEditViewBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Compiler pass to add View Builders to ViewBuilderRegistry.
 */
class ViewBuilderRegistryPass implements CompilerPassInterface
{
    const VIEW_BUILDER_REGISTRY = 'ezpublish.view_builder.registry';
    const VIEW_BUILDER_CONTENT_EDIT = ContentEditViewBuilder::class;

    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition(self::VIEW_BUILDER_REGISTRY) || !$container->hasDefinition(ContentEditViewBuilder::class)) {
            return;
        }

        $registry = $container->findDefinition(self::VIEW_BUILDER_REGISTRY);

        $viewBuilders = [
            $container->getDefinition(self::VIEW_BUILDER_CONTENT_EDIT),
        ];

        $registry->addMethodCall('addToRegistry', [$viewBuilders]);
    }
}
