<?php

declare(strict_types=1);

namespace WakeOnWeb\EventBusPublisher\App\Bundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    const DELIVERY_MODE_ASYNC = 'asynchronous';
    const DELIVERY_MODE_SYNC = 'synchronous';

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('wakeonweb_event_bus_publisher')
            ->children()
                ->arrayNode('publishing')
                    ->validate()
                        ->ifTrue(function($v) {
                            return $v['delivery_mode'] === static::DELIVERY_MODE_SYNC && isset($v['queue_name']);
                        })
                        ->thenInvalid('Queue name has to be defined only if delivery_mode is asynchronous')
                        ->ifTrue(function($v) {
                            return $v['delivery_mode'] === static::DELIVERY_MODE_ASYNC && false === isset($v['queue_name']);
                        })
                        ->thenInvalid('Queue name has to be defined if delivery_mode is asynchronous')
                    ->end()
                    ->isRequired()
                    ->children()
                        ->enumNode('delivery_mode')->isRequired()->values([static::DELIVERY_MODE_SYNC, static::DELIVERY_MODE_ASYNC])->defaultValue(static::DELIVERY_MODE_SYNC)->end()
                        ->scalarNode('queue_name')->end()
                        ->arrayNode('listened_prooph_buses')
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
                    ->useAttributeAsKey('target')
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
                    ->useAttributeAsKey('target')
                    ->prototype('array')
                        ->prototype('scalar')
                        ->end()
                    ->end()
                ->end()
            ->end();

    }
}
