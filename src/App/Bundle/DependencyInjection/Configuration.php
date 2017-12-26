<?php

declare(strict_types=1);

namespace WakeOnWeb\EventBusPublisher\App\Bundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('wakeonweb_event_bus_publisher')
            ->children()
                ->arrayNode('publishing')
                    ->isRequired()
                    ->children()
                        ->scalarNode('queue_name')->isRequired()->end()
                        ->arrayNode('prooph_buses')
                            ->isRequired()
                            ->prototype('scalar')
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('driver')
                    ->children()
                        ->append($this->createInMemoryDriver())
                    ->end()
                ->end()
            ->end()
            ;

        return $treeBuilder;
    }

    private function createInMemoryDriver()
    {
        return (new ArrayNodeDefinition('in_memory'))
            ->children()
                ->arrayNode('targets')
                    ->prototype('array')
                        ->validate()
                            ->ifTrue(function($v) {
                                $gateways = ['service', 'http', 'amqp'];

                                return count(array_intersect(array_keys($v), $gateways)) !== 1;
                            })
                            ->thenInvalid('You must define 1 way to publish on targets, %s')
                        ->end()
                        ->children()
                            ->scalarNode('normalizer')->end()
                            ->arrayNode('service')
                                ->children()
                                    ->scalarNode('id')->isRequired()->end()
                                ->end()
                            ->end()
                            ->arrayNode('http')
                                ->children()
                                    ->scalarNode('endpoint')->isRequired()->end()
                                ->end()
                            ->end()
                            ->arrayNode('amqp')
                                ->children()
                                    ->scalarNode('queue')->isRequired()->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                //@todo verify than target are well defined in routing.
                ->arrayNode('routing')
                    ->prototype('array')
                        ->prototype('scalar')
                        ->end()
                    ->end()
                ->end()
            ->end();

    }
}
