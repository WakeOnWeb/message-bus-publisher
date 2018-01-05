<?php

namespace WakeOnWeb\EventBusPublisher\Infra\Publishing\Delivery;

use Bernard\Message\PlainMessage;
use Bernard\Producer as BernardProducer;
use Prooph\Common\Messaging\DomainEvent;
use WakeOnWeb\EventBusPublisher\Domain\Publishing\Delivery\DeliveryInterface;
use WakeOnWeb\EventBusPublisher\Infra\Queue\BernardReceiver;

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
    public function deliver(DomainEvent $event, string $targetId): void
    {
        $bernardMessage = new PlainMessage(BernardReceiver::MESSAGE_NAME, [
            'target' => $targetId,
            'domain_event' => serialize($event),
        ]);

        $this->bernardProducer->produce($bernardMessage, $this->guessQueueName($targetId));
    }

    private function guessQueueName($targetName)
    {
        return str_replace(['{target}'], [$targetName], $this->queueName);
    }
}
