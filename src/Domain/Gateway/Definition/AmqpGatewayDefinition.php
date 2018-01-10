<?php

namespace WakeOnWeb\EventBusPublisher\Domain\Gateway\Definition;

class AmqpGatewayDefinition implements GatewayDefinitionInterface
{
    const TYPE = 'amqp';

    /** @var string */
    private $queueName;

    /** @var string */
    private $messageName;

    public function __construct(string $queueName, string $messageName)
    {
        $this->queueName = $queueName;
        $this->messageName = $messageName;
    }

    public function getQueueName(): string
    {
        return $this->queueName;
    }

    public function getMessageName(): string
    {
        return $this->messageName;
    }

    public static function createFromArray(array $data): AmqpGatewayDefinition
    {
        return new static($data['queue_name'], $data['message_name']);
    }

    public function jsonSerialize()
    {
        return [
            '_type' => static::TYPE,
            'queue_name' => $this->queueName,
            'message_name' => $this->messageName,
        ];
    }
}
