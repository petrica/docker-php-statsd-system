<?php
/**
 * Created by PhpStorm.
 * User: Petrica
 * Date: 3/26/2016
 * Time: 1:25
 */
namespace Petrica\StatsdSystem\Config\Definition;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class ConfigDefinition
 * @package Petrica\StatsdSystem\Config\Definition
 */
class ConfigDefinition implements ConfigurationInterface
{
    /**
     * Provides configuration mapping
     *
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('gauges');
        $rootNode
            ->useAttributeAsKey('path')
            ->prototype('array')
            ->children()
                ->scalarNode('class')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->variableNode('arguments')->end()
            ->end();

        return $treeBuilder;
    }
}
