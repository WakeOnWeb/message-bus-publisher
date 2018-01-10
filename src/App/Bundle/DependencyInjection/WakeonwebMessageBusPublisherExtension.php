<?php

declare(strict_types=1);

namespace WakeOnWeb\MessageBusPublisher\App\Bundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use WakeOnWeb\MessageBusPublisher\Domain\Audit\AuditorAggregator;
use WakeOnWeb\MessageBusPublisher\Domain\Gateway\Definition as GatewayDefinition;
use WakeOnWeb\MessageBusPublisher\Domain\Target\Target;
use WakeOnWeb\MessageBusPublisher\Infra;

/**
 * Defines and load message bus instances.
 */
final class WakeonwebMessageBusPublisherExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config/services'));
        $loader->load('delivery.xml');
        $loader->load('gateway.xml');
        $loader->load('normalizers.xml');

        $driver = current(array_keys($config['driver']));
        $driverConfig = current($config['driver']);

        switch ($driver) {
            case 'doctrine_orm':
                $container->setParameter('wow.message_bus_publisher.driver.doctrine_orm.route_entity_class', $driverConfig['route_entity']);
                $container->setParameter('wow.message_bus_publisher.driver.doctrine_orm.target_entity_class', $driverConfig['target_entity']);
                $container->setAlias('wow.message_bus_publisher.driver.doctrine_orm.entity_manager', sprintf('doctrine.orm.%s_entity_manager', $driverConfig['entity_manager']));
                $loader->load('driver_doctrine_orm.xml');
                break;
            case 'in_memory':
                $this->createInMemoryDriverDefinitions($config['driver']['in_memory'], $container);
                break;
            default:
                throw new \LogicException("Unknown driver $driver");
                break;
        }

        if ($config['publishing']['delivery_mode'] === Configuration::DELIVERY_MODE_ASYNC) {
            $loader->load('bernard.xml');

            $publishingDelivery = new Definition(Infra\Publishing\Delivery\BernardAsynchronous::class, [
                new Reference('bernard.producer'),
                $config['publishing']['queue_name'],
            ]);
        } else {
            $publishingDelivery = new Reference('wow.message_bus_publisher.publishing.delivery.synchronous');
        }

        $proophPluginDefinition = new Definition(Infra\Publishing\ProophMessageBusPlugin::class, [
            new Reference('wow.message_bus_publisher.message_router'),
            $publishingDelivery,
            new Reference('wow.message_bus_publisher.auditor', ContainerInterface::NULL_ON_INVALID_REFERENCE),
        ]);

        foreach ($config['publishing']['listened_prooph_buses'] as $bus) {
            $proophPluginDefinition->addTag(sprintf('prooph_service_bus.%s.plugin', $bus));
        }

        $container->setDefinition('wow.message_bus_publisher.prooph_plugin', $proophPluginDefinition);

        if (array_key_exists('audit', $config)) {
            $this->createAuditDefinition($config['audit'], $container);
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
                $gatewayDefinition = new Definition(GatewayDefinition\ServiceGatewayDefinition::class, [$targetConfig['service']['id']]);
            } elseif (array_key_exists('http', $targetConfig)) {
                $gatewayDefinition = new Definition(GatewayDefinition\HttpGatewayDefinition::class, [$targetConfig['http']['endpoint']]);
            } elseif (array_key_exists('amqp', $targetConfig)) {
                $gatewayDefinition = new Definition(GatewayDefinition\AmqpGatewayDefinition::class, [$targetConfig['amqp']['queue'], $targetConfig['amqp']['message_name']]);
            } else {
                throw new \LogicException(sprintf('Cannot guess gateway of target “%s“', $name));
            }

            $targetDefinitions[] = new Definition(Target::class, [$name, $gatewayDefinition, $targetConfig['normalizer']]);
        }

        $targetRepository = new Definition(Infra\Target\InMemory\TargetRepository::class, [$targetDefinitions]);
        $targetRepository->setPublic(true);

        $container->setDefinition('wow.message_bus_publisher.in_memory_target_repository', $targetRepository);
        $container->setAlias('wow.message_bus_publisher.target_repository', 'wow.message_bus_publisher.in_memory_target_repository');
    }

    private function createInMemoryRouterDefinition(array $config, ContainerBuilder $container)
    {
        $definition = new Definition(Infra\Router\InMemory\MessageRouter::class);

        foreach ($config as $targetName => $messages) {
            foreach ($messages as $message) {
                $definition->addMethodCall('addRoute', [$message, $targetName]);
            }
        }
        $definition->setPublic(true);

        $container->setDefinition('wow.message_bus_publisher.in_memory_message_router', $definition);
        $container->setAlias('wow.message_bus_publisher.message_router', 'wow.message_bus_publisher.in_memory_message_router');
    }

    private function createAuditDefinition(array $config, ContainerBuilder $container)
    {
        $auditors = [];

        foreach ($config['drivers'] as $driver => $driverConfig) {
            switch ($driver) {
                case 'monolog':
                    $loggerDefinition = new Definition(Infra\Audit\PsrLogger\Auditor::class, [new Reference('logger'), $driverConfig['only_routed_messages'], $driverConfig['level']]);
                    $loggerDefinition->addTag('monolog.logger', ['channel' => 'wow.message_bus_publisher.audit']);

                    $container->setDefinition('wow.message_bus_publisher.auditor.monolog', $loggerDefinition);

                    $auditors[] = new Reference('wow.message_bus_publisher.auditor.monolog');
                    break;
                case 'doctrine_orm':
                    $auditors[] = new Definition(Infra\Audit\DoctrineORM\Auditor::class, [
                        new Reference(sprintf('doctrine.orm.%s_entity_manager', $driverConfig['entity_manager'])),
                        $driverConfig['listened_message_entity'],
                        $driverConfig['targeted_message_entity'],
                        $driverConfig['only_routed_messages'],
                    ]);
                    break;
                case 'services':
                    foreach ($driverConfig as $service) {
                        $auditors[] = new Reference($service);
                    }
                    break;
            }
        }

        $container->setDefinition('wow.message_bus_publisher.auditor', new Definition(AuditorAggregator::class, [$auditors]));
    }
}
