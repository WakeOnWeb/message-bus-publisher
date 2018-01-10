<?php

namespace WakeOnWeb\MessageBusPublisher\Domain\Publishing\Delivery;

use Prooph\Common\Messaging\DomainMessage;

/**
 * DeliveryInterface.
 *
 * @author Stephane PY <s.py@wakeonweb.com>
 */
interface DeliveryInterface
{
    /**
     * Delivery an message to a target.
     *
     * @param DomainMessage $message    message
     * @param string      $targetId targetId
     */
    public function deliver(DomainMessage $message, string $targetId): void;
}
