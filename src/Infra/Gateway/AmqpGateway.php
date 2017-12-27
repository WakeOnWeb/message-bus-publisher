<?php

namespace WakeOnWeb\EventBusPublisher\Infra\Gateway;

use WakeOnWeb\EventBusPublisher\Domain\Gateway\GatewayInterface;
use WakeOnWeb\EventBusPublisher\Domain\Gateway\GatewayResponse;
use Bernard\Producer as BernardProducer;
use Bernard\Message\PlainMessage;

class AmqpGateway implements GatewayInterface
{
    const MESSAGE_NAME = 'EventBusExternalMessage';

    private $bernardProducer;
    private $queueName;

    public function __construct(BernardProducer $bernardProducer, $queueName)
    {
        $this->bernardProducer = $bernardProducer;
        $this->queueName = $queueName;
    }

    public function send($message): GatewayResponse
    {
        $bernardMessage = new PlainMessage(self::MESSAGE_NAME, ['message' => $message]);

        $this->bernardProducer->produce($bernardMessage, $this->queueName);

        return GatewayResponse::createSuccessfulResponse();
    }
}
