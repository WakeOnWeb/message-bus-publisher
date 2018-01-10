<?php

namespace WakeOnWeb\MessageBusPublisher\Infra\Publishing\Delivery;

use Bernard\Message\PlainMessage;
use Bernard\Producer as BernardProducer;
use Prooph\Common\Messaging\DomainMessage;
use WakeOnWeb\MessageBusPublisher\Domain\Publishing\Delivery\DeliveryInterface;
use WakeOnWeb\MessageBusPublisher\Infra\Queue\BernardReceiver;

class BernardAsynchronous implements DeliveryInterface
{
    /** @var BernardProducer */
    private $bernardProducer;

    /** @var string */
    private $queueName;

    /**
     * @param BernardProducer $bernardProducer bernardProducer
     * @param string          $queueName       queueName
     */
    public function __construct(BernardProducer $bernardProducer, string $queueName)
    {
        $this->bernardProducer = $bernardProducer;
        $this->queueName = $queueName;
    }

    /**
     * {@inheritdoc}
     */
    public function deliver(DomainMessage $message, string $targetId): void
    {
        $bernardMessage = new PlainMessage(BernardReceiver::MESSAGE_NAME, [
            'target' => $targetId,
            'domain_message' => serialize($message),
        ]);

        $this->bernardProducer->produce($bernardMessage, $this->guessQueueName($targetId));
    }

    private function guessQueueName($targetName)
    {
        return str_replace(['{target}'], [$targetName], $this->queueName);
    }
}
