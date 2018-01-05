<?php

namespace WakeOnWeb\EventBusPublisher\Infra\Gateway;

use Psr\Container\ContainerInterface;
use WakeOnWeb\EventBusPublisher\Domain\Gateway\Definition;
use WakeOnWeb\EventBusPublisher\Domain\Gateway\Definition\GatewayDefinitionInterface;
use WakeOnWeb\EventBusPublisher\Domain\Gateway\GatewayFactoryInterface;
use WakeOnWeb\EventBusPublisher\Domain\Gateway\GatewayInterface;

class LazyContainerGatewayFactory implements GatewayFactoryInterface
{
    /** @var ContainerInterface */
    private $container;

    /**
     * @param ContainerInterface $container container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function createFromDefinition(GatewayDefinitionInterface $gatewayDefinition): GatewayInterface
    {
        if ($gatewayDefinition instanceof Definition\HttpGatewayDefinition) {
            return $this->createHttpGatewayFromDefinition($gatewayDefinition);
        }

        if ($gatewayDefinition instanceof Definition\AmqpGatewayDefinition) {
            return $this->createAmqpGatewayFromDefinition($gatewayDefinition);
        }

        if ($gatewayDefinition instanceof Definition\ServiceGatewayDefinition) {
            return $this->createServiceGatewayFromDefinition($gatewayDefinition);
        }

        throw new \InvalidArgumentException('Factory cannot resolve this gateway definition');
    }

    private function createHttpGatewayFromDefinition(Definition\HttpGatewayDefinition $gatewayDefinition): GatewayInterface
    {
        $gateway = $this->container->get('wow.event_bus_publisher.gateway.http');
        $gateway->configure($gatewayDefinition->getEndpoint());

        return $gateway;
    }

    private function createAmqpGatewayFromDefinition(Definition\AmqpGatewayDefinition $gatewayDefinition): GatewayInterface
    {
        $gateway = $this->container->get('wow.event_bus_publisher.gateway.amqp');
        $gateway->configure($gatewayDefinition->getQueueName());

        return $gateway;
    }

    private function createServiceGatewayFromDefinition(Definition\ServiceGatewayDefinition $gatewayDefinition): GatewayInterface
    {
        return $this->container->get($gatewayDefinition->getService());
    }
}
