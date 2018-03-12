<?php
/**
 * This file is part of the eZ RepositoryForms package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryFormsBundle\DependencyInjection\Configuration\Parser;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\AbstractParser;
use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\SiteAccessAware\ContextualizerInterface;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;

class ContentEdit extends AbstractParser
{
    /**
     * Adds semantic configuration definition.
     *
     * @param \Symfony\Component\Config\Definition\Builder\NodeBuilder $nodeBuilder Node just under ezpublish.system.<siteaccess>
     */
    public function addSemanticConfig(NodeBuilder $nodeBuilder)
    {
        $nodeBuilder
            ->arrayNode('content_edit')
                ->info('Content edit configuration.')
                ->setDeprecated('This key was deprecated in 2.1 and will be removed in 3.0. Please use siteaccess aware configuration.')
                ->children()
                    ->arrayNode('templates')
                        ->info('Content edit templates.')
                        ->children()
                            ->scalarNode('edit')
                                ->info('Template to use for content edit form rendering.')
                            ->end()
                            ->scalarNode('create')
                                ->info('Template to use for content create form rendering.')
                            ->end()
                            ->scalarNode('create_draft')
                                ->info('Template to use for content draft creation rendering.')
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    public function mapConfig(array &$scopeSettings, $currentScope, ContextualizerInterface $contextualizer)
    {
        if (empty($scopeSettings['content_edit'])) {
            return;
        }

        $settings = $scopeSettings['content_edit'];

        if (!empty($settings['templates']['edit'])) {
            $contextualizer->setContextualParameter(
                'content_edit.templates.edit',
                $currentScope,
                $settings['templates']['edit']
            );
        }

        if (!empty($settings['templates']['create'])) {
            $contextualizer->setContextualParameter(
                'content_edit.templates.create',
                $currentScope,
                $settings['templates']['create']
            );
        }

        if (!empty($settings['templates']['create_draft'])) {
            $contextualizer->setContextualParameter(
                'content_edit.templates.create_draft',
                $currentScope,
                $settings['templates']['create_draft']
            );
        }
    }
}
