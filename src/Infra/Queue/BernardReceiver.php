<?php

namespace WakeOnWeb\EventBusPublisher\Infra\Queue;

use Bernard\Message\PlainMessage;
use WakeOnWeb\EventBusPublisher\Domain\Target\TargetRepositoryInterface;
use WakeOnWeb\EventBusPublisher\Domain\Publishing\Delivery\DeliveryInterface;

class BernardReceiver
{
    const MESSAGE_NAME = 'DomainEvent';

    /** @var DeliveryInterface */
    private $delivery;

    /**
     * @param DeliveryInterface $delivery delivery
     */
    public function __construct(DeliveryInterface $delivery)
    {
        $this->delivery = $delivery;
    }

    /**
     * @param PlainMessage $message
     */
    public function __invoke(PlainMessage $message)
    {
        return $this->delivery->deliver(unserialize($message->get('domain_event')), $message->get('target'));
    }
}
