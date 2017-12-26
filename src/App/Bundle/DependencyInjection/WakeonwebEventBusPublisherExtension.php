<?php
declare(strict_types=1);

namespace WakeOnWeb\EventBusPublisher\App\Bundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use WakeOnWeb\EventBusPublisher\Domain\Target\Target;
use WakeOnWeb\EventBusPublisher\Infra\Gateway\AmqpGateway;
use WakeOnWeb\EventBusPublisher\Infra\Gateway\HttpGateway;
use WakeOnWeb\EventBusPublisher\Infra\Router\InMemoryEventRouter;
use WakeOnWeb\EventBusPublisher\Infra\Target\InMemoryTargetRepository;

/**
 * Defines and load message bus instances.
 */
final class WakeonwebEventBusPublisherExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $configs);

        $this->createInMemoryDriverDefinitions($config['driver']['in_memory'], $container);

        $container->setParameter('wow.event_bus_publisher.publishing.queue_name', $config['publishing']['queue_name']);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config/services'));
        $loader->load('bernard.xml');
        $loader->load('normalizers.xml');
        $loader->load('prooph_plugin.xml');

        foreach ($config['publishing']['prooph_buses'] as $bus) {
            $container->getDefinition('wow.event_bus_publisher.prooph_plugin')
                ->addTag(sprintf('prooph_service_bus.%s.plugin', $bus));
            ;
        }
    }

    private function createInMemoryDriverDefinitions(array $config, ContainerBuilder $container)
    {
        $this->createInMemoryTargetRepositoryDefinition($config['targets'], $container);
        $this->createInMemoryRouterDefinition($config['routing'], $container);
    }

    private function createInMemoryTargetRepositoryDefinition(array $config, ContainerBuilder $container)
    {
        $targetDefinitions = [];
        foreach ($config as $name => $targetConfig) {
            if (array_key_exists('service', $targetConfig)) {
                $gatewayDefinition = new Reference($targetConfig['service']['id']);
            } elseif (array_key_exists('http', $targetConfig)) {
                $gatewayDefinition = new Definition(HttpGateway::class, [$targetConfig['http']['endpoint']]);
            } elseif (array_key_exists('amqp', $targetConfig)) {
                $gatewayDefinition = new Definition(AmqpGateway::class, [new Reference('bernard.producer'), $targetConfig['amqp']['queue']]);
            } else {
                throw new \LogicException(sprintf('Cannot guess gateway of target “%s“', $name));
            }


            $normalizerDefinition = null;
            if (array_key_exists('normalizer', $targetConfig)) {
                $normalizerDefinition = new Reference($targetConfig['normalizer']);
            }

            $targetDefinitions[] = new Definition(Target::class, [$name, $gatewayDefinition, $normalizerDefinition]);
        }

        $targetRepository = new Definition(InMemoryTargetRepository::class, [$targetDefinitions]);

        $container->setDefinition('wow.event_bus_publisher.in_memory_target_repository', $targetRepository);
        $container->setAlias('wow.event_bus_publisher.target_repository', 'wow.event_bus_publisher.in_memory_target_repository');
    }

    private function createInMemoryRouterDefinition(array $config, ContainerBuilder $container)
    {
        $definition = new Definition(InMemoryEventRouter::class, [new Reference('wow.event_bus_publisher.target_repository')]);

        foreach ($config as $targetName => $events) {
            foreach ($events as $event) {
                $definition->addMethodCall('addRoute', [$event, $targetName]);
            }
        }

        $container->setDefinition('wow.event_bus_publisher.in_memory_router_repository', $definition);
        $container->setAlias('wow.event_bus_publisher.router_repository', 'wow.event_bus_publisher.in_memory_router_repository');
    }
}
