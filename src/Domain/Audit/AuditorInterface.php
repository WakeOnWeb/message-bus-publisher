<?php

namespace WakeOnWeb\EventBusPublisher\Domain\Audit;

use Prooph\Common\Messaging\DomainEvent;
use WakeOnWeb\EventBusPublisher\Domain\Gateway\GatewayResponse;

/**
 * AuditorInterface.
 *
 * @author Stephane PY <s.py@wakeonweb.com>
 */
interface AuditorInterface
{
    public function registerListenedEvent(DomainEvent $event, bool $routed);

    public function registerTargetedEvent(DomainEvent $event, string $targetId, GatewayResponse $gatewayResponse);
}
