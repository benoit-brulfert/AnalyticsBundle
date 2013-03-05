<?php

namespace Cethyworks\AnalyticsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class Configuration
{
    /**
     * Generates the configuration tree.
     *
     * @return \Symfony\Component\Config\Definition\ArrayNode The config tree
     */
    public function getConfigTree()
    {
        $tree = new TreeBuilder();
        $rootNode = $tree->root('cethyworks_analytics');

        $rootNode
            ->children()
            ->arrayNode('environments')
                ->prototype('scalar')->end()
            ->end()
            ->arrayNode('trackers')
                ->requiresAtLeastOneElement()
                ->useAttributeAsKey('name')
                ->prototype('array')
                    ->children()
                        ->scalarNode('type')->end()
                        ->scalarNode('class')->defaultNull()->end()
                        ->scalarNode('template')->defaultNull()->end()
                        ->arrayNode('params')
                            ->children()
                                ->scalarNode('url')->end()
                                ->scalarNode('site_id')->end()
                                ->scalarNode('account')->end()
                            ->end()
                        ->end()
                        ->arrayNode('environments')
                            ->addDefaultsIfNotSet()
                            ->defaultValue(array())
                            ->prototype('scalar')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
            ->arrayNode('segmentIo')
               ->children()
                       ->scalarNode('account_id')
                ->end()
            ->end()
        ;

        return $tree->buildTree();
    }
}

