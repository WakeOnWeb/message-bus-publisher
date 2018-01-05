<?php

namespace WakeOnWeb\EventBusPublisher\Domain\Gateway\Definition;

class HttpGatewayDefinition implements GatewayDefinitionInterface
{
    const TYPE = 'http';

    /** @var string */
    private $endpoint;

    public function __construct(string $endpoint)
    {
        $this->endpoint = $endpoint;
    }

    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    public static function createFromArray(array $data): HttpGatewayDefinition
    {
        return new static($data['endpoint']);
    }

    public function jsonSerialize()
    {
        return [
            '_type' => static::TYPE,
            'endpoint' => $this->endpoint,
        ];
    }
}
