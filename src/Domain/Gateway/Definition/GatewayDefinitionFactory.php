<?php

namespace WakeOnWeb\MessageBusPublisher\Domain\Gateway\Definition;

abstract class GatewayDefinitionFactory
{
    public static function createFromArray(array $data): GatewayDefinitionInterface
    {
        if (false === array_key_exists('_type', $data)) {
            throw new \LogicException('Cannot create gateway definition, invalid payload');
        }

        switch ($data['_type']) {
            case AmqpGatewayDefinition::TYPE:
                return AmqpGatewayDefinition::createFromArray($data);
                break;
            case HttpGatewayDefinition::TYPE:
                return HttpGatewayDefinition::createFromArray($data);
                break;
            case ServiceGatewayDefinition::TYPE:
                return ServiceGatewayDefinition::createFromArray($data);
                break;
        }

        throw new \LogicException('Unknown type '.$data['_type']);
    }
}
