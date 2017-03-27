<?php

namespace BiteCodes\MonologUIBundle\DependencyInjection;

use Monolog\Logger;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception\InvalidTypeException;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('bite_codes_monolog_ui');

        $rootNode
            ->children()
                ->scalarNode('table')->defaultValue('monolog_ui_logs')->end()
                ->scalarNode('connection')->isRequired()->end()
                ->arrayNode('logger')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->append($this->getLogConfig())
                    ->end()
                ->end()
            ->end();

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

        return $treeBuilder;
    }

    private function getLogConfig()
    {
        $node = new ArrayNodeDefinition('handles');

        $nodes = [
            Logger::getLevelName(Logger::EMERGENCY) => null,
            Logger::getLevelName(Logger::ALERT) => null,
            Logger::getLevelName(Logger::CRITICAL) => null,
            Logger::getLevelName(Logger::ERROR) => null,
            Logger::getLevelName(Logger::WARNING) => null,
            Logger::getLevelName(Logger::NOTICE) => [],
            Logger::getLevelName(Logger::INFO) => [],
            Logger::getLevelName(Logger::DEBUG) => [],
        ];

        $node = $node
            ->addDefaultsIfNotSet()
            ->children();

        foreach ($nodes as $nodeName => $default) {
            $node = $node
                ->variableNode(strtolower($nodeName))
                    ->validate()
                        ->always(function ($v) {
                            if (is_null($v) || is_array($v)) {
                                return $v;
                            }
                            throw new InvalidTypeException();
                        })
                    ->end()
                    ->defaultValue($default)
                ->end();
        }

        $node = $node
            ->end();

        return $node;
    }
}
