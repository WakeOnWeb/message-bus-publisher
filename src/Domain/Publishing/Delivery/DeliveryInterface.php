<?php

namespace WakeOnWeb\EventBusPublisher\Domain\Publishing\Delivery;

use Prooph\Common\Messaging\DomainEvent;

/**
 * DeliveryInterface
 *
 * @author Stephane PY <s.py@wakeonweb.com>
 */
interface DeliveryInterface
{
    /**
     * Delivery an event to a target.
     *
     * @param DomainEvent $event event
     * @param string $targetId targetId
     */
    public function deliver(DomainEvent $event, string $targetId): void;
}
