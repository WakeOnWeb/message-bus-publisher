<?php

namespace WakeOnWeb\MessageBusPublisher\Infra\Gateway;

use WakeOnWeb\MessageBusPublisher\Domain\Gateway\GatewayInterface;
use WakeOnWeb\MessageBusPublisher\Domain\Gateway\GatewayResponse;
use Bernard\Producer as BernardProducer;
use Bernard\Message\PlainMessage;

class AmqpGateway implements GatewayInterface
{
    private $bernardProducer;
    private $queueName;
    private $messageName;

    public function __construct(BernardProducer $bernardProducer)
    {
        $this->bernardProducer = $bernardProducer;
    }

    public function configure(string $queueName, string $messageName)
    {
        $this->queueName = $queueName;
        $this->messageName = $messageName;
    }

    public function send($message): GatewayResponse
    {
        $bernardMessage = new PlainMessage($this->messageName, ['message' => $message]);

        $this->bernardProducer->produce($bernardMessage, $this->queueName);

        return GatewayResponse::createSuccessfulResponse();
    }
}
