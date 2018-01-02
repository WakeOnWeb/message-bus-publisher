<?php
declare(strict_types=1);

namespace WakeOnWeb\EventBusPublisher\App\Bundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use WakeOnWeb\EventBusPublisher\Domain\Target\Target;
use WakeOnWeb\EventBusPublisher\Infra\Gateway\AmqpGateway;
use WakeOnWeb\EventBusPublisher\Infra\Gateway\HttpGateway;
use WakeOnWeb\EventBusPublisher\Infra\Publishing\Delivery;
use WakeOnWeb\EventBusPublisher\Infra\Publishing\ProophEventBusPlugin;
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

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config/services'));
        $loader->load('delivery.xml');
        $loader->load('normalizers.xml');

        if ($config['publishing']['delivery_mode'] === Configuration::DELIVERY_MODE_ASYNC) {
            $loader->load('bernard.xml');

            $publishingDelivery = new Definition(Delivery\BernardAsynchronous::class, [
                new Reference('bernard.producer'),
                $config['publishing']['queue_name']
            ]);
        } else {
            $publishingDelivery = new Reference('wow.event_bus_publisher.publishing.delivery.synchronous');
        }

        $proophPluginDefinition = new Definition(ProophEventBusPlugin::class, [
            new Reference('wow.event_bus_publisher.router_repository'),
            $publishingDelivery
        ]);

        foreach ($config['publishing']['listened_prooph_buses'] as $bus) {
            $proophPluginDefinition->addTag(sprintf('prooph_service_bus.%s.plugin', $bus));
        }

        $container->setDefinition('wow.event_bus_publisher.prooph_plugin', $proophPluginDefinition);
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
        $targetRepository->setPublic(true);

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
        $definition->setPublic(true);

        $container->setDefinition('wow.event_bus_publisher.in_memory_router_repository', $definition);
        $container->setAlias('wow.event_bus_publisher.router_repository', 'wow.event_bus_publisher.in_memory_router_repository');
    }
}
