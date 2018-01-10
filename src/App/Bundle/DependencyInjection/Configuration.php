<?php

declare(strict_types=1);

namespace WakeOnWeb\EventBusPublisher\App\Bundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use WakeOnWeb\EventBusPublisher\Infra\Audit\DoctrineORM\Entity as AuditEntity;
use WakeOnWeb\EventBusPublisher\Infra\Router\DoctrineORM\Entity as RouterEntity;

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
                        ->ifTrue(function ($v) {
                            return $v['delivery_mode'] === static::DELIVERY_MODE_SYNC && isset($v['queue_name']);
                        })
                        ->thenInvalid('Queue name has to be defined only if delivery_mode is asynchronous')
                        ->ifTrue(function ($v) {
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
                ->arrayNode('audit')
                    ->validate()
                        ->ifTrue(function ($v) { return empty($v); })
                        ->thenInvalid('You must define at least one audit driver or avoid audit.')
                    ->end()
                    ->children()
                        ->arrayNode('drivers')
                            ->children()
                                ->arrayNode('monolog')
                                    ->children()
                                        ->booleanNode('only_routed_events')->defaultFalse()->end()
                                        ->scalarNode('level')->defaultValue('notice')->end()
                                    ->end()
                                ->end()
                                ->arrayNode('doctrine_orm')
                                    ->children()
                                        ->booleanNode('only_routed_events')->defaultFalse()->end()
                                        ->scalarNode('entity_manager')->defaultValue('default')->cannotBeEmpty()->end()
                                        ->scalarNode('listened_event_entity')->defaultValue(AuditEntity\ListenedEvent::class)->cannotBeEmpty()->end()
                                        ->scalarNode('targeted_event_entity')->defaultValue(AuditEntity\TargetedEventWithResponse::class)->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                                ->arrayNode('services')
                                    ->prototype('scalar')->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('driver')
                    ->validate()
                        ->ifTrue(function ($v) {
                            return 1 !== count(array_keys($v));
                        })
                        ->thenInvalid('wakeonweb_event_bus_publisher: You must define only one driver.')
                    ->end()
                    ->children()
                        ->append($this->createInMemoryDriver())
                        ->append($this->createDoctrineORMDriver())
                    ->end()
                ->end()
            ->end()
            ;

        return $treeBuilder;
    }

    private function createDoctrineORMDriver()
    {
        return (new ArrayNodeDefinition('doctrine_orm'))
                ->children()
                    ->scalarNode('entity_manager')->defaultValue('default')->cannotBeEmpty()->end()
                    ->scalarNode('target_entity')->defaultValue(RouterEntity\Target::class)->cannotBeEmpty()->end()
                    ->scalarNode('route_entity')->defaultValue(RouterEntity\Route::class)->cannotBeEmpty()->end()
                ->end()
            ;
    }

    private function createInMemoryDriver()
    {
        return (new ArrayNodeDefinition('in_memory'))
            ->children()
                ->arrayNode('targets')
                    ->useAttributeAsKey('target')
                    ->prototype('array')
                        ->validate()
                            ->ifTrue(function ($v) {
                                $gateways = ['service', 'http', 'amqp'];

                                return 1 !== count(array_intersect(array_keys($v), $gateways));
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
                                    ->scalarNode('message_name')->defaultValue('EventBusExternalMessage')->cannotBeEmpty()->end()
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
