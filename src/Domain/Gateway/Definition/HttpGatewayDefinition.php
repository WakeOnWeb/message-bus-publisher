<?php

namespace WakeOnWeb\MessageBusPublisher\Domain\Gateway\Definition;

class HttpGatewayDefinition implements GatewayDefinitionInterface
{
    const TYPE = 'http';

    /** @var string */
    private $endpoint;

    /** @var string */
    private $contentType;

    public function __construct(string $endpoint, string $contentType)
    {
        $this->endpoint = $endpoint;
        $this->contentType = $contentType;
    }

    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    public function getContentType(): string
    {
        return $this->contentType;
    }

    public static function createFromArray(array $data): HttpGatewayDefinition
    {
        return new static($data['endpoint'], $data['content_type']);
    }

    public function jsonSerialize()
    {
        return [
            '_type' => static::TYPE,
            'endpoint' => $this->endpoint,
            'content_type' => $this->contentType,
        ];
    }
}
