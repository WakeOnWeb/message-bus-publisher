<?php

namespace WakeOnWeb\MessageBusPublisher\Infra\Queue;

use Bernard\Message\PlainMessage;
use WakeOnWeb\MessageBusPublisher\Domain\Publishing\Delivery\DeliveryInterface;

class BernardReceiver
{
    const MESSAGE_NAME = 'DomainMessage';

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
        return $this->delivery->deliver(unserialize($message->get('domain_message')), $message->get('target'));
    }
}
