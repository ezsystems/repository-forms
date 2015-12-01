<?php
/**
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\RepositoryFormsBundle\DependencyInjection\Configuration\Parser;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\AbstractParser;
use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\SiteAccessAware\ContextualizerInterface;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;

/**
 * Declares and parses the content_edit_view semantic config.
 */
class ContentEditView extends AbstractParser
{
    public function mapConfig(array &$scopeSettings, $currentScope, ContextualizerInterface $contextualizer)
    {
    }

    public function preMap(array $config, ContextualizerInterface $contextualizer)
    {
        $contextualizer->mapConfigArray('content_edit_view', $config, ContextualizerInterface::MERGE_FROM_SECOND_LEVEL);
    }

    public function addSemanticConfig(NodeBuilder $nodeBuilder)
    {
        $nodeBuilder
            ->arrayNode('content_edit_view')
                ->info('View selection settings when displaying a content edit view')
                ->children()
                ->arrayNode('full')
                    ->useAttributeAsKey('view_name')
                    ->normalizeKeys(false)
                    ->prototype('array')
                        ->children()
                            ->scalarNode('template')
                                ->isRequired()
                                ->info('Custom template path to use for rendering')
                                ->example('subdir/my_template.html.twig')
                            ->end()
                            ->scalarNode('controller')
                                ->info(
                                    <<<EOT
Use custom controller instead of the default one to display a content edit matching your rules.
You can use the controller reference notation supported by Symfony.
EOT
                                )
                                ->example('MyBundle:MyControllerClass:editArticleAction')
                            ->end()
                            ->arrayNode('match')
                                ->info('Condition matchers configuration')
                                ->useAttributeAsKey('view_name')
                                ->prototype('variable')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
            ->beforeNormalization()
            ->always()
            // Add one 'block' level in order to match the other view internal config structure.
            ->then(function ($v) { return array('full' => $v); })->end()
            ->end();
    }
}
