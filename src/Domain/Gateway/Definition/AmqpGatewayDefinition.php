<?php

namespace WakeOnWeb\EventBusPublisher\Domain\Gateway\Definition;

class AmqpGatewayDefinition implements GatewayDefinitionInterface
{
    const TYPE = 'amqp';

    /** @var string */
    private $queueName;

    public function __construct(string $queueName)
    {
        $this->queueName = $queueName;
    }

    public function getQueueName(): string
    {
        return $this->queueName;
    }

    public static function createFromArray(array $data): AmqpGatewayDefinition
    {
        return new static($data['queue_name']);
    }

    public function jsonSerialize()
    {
        return [
            '_type' => static::TYPE,
            'queue_name' => $this->queueName,
        ];
    }
}
