<?php

namespace WakeOnWeb\MessageBusPublisher\Domain\Gateway\Definition;

class ServiceGatewayDefinition implements GatewayDefinitionInterface
{
    const TYPE = 'service';

    /** @var string */
    private $service;

    public function __construct(string $service)
    {
        $this->service = $service;
    }

    public function getService(): string
    {
        return $this->service;
    }

    public static function createFromArray(array $data): ServiceGatewayDefinition
    {
        return new static($data['service']);
    }

    public function jsonSerialize()
    {
        return [
            '_type' => static::TYPE,
            'service' => $this->servicek,
        ];
    }
}
