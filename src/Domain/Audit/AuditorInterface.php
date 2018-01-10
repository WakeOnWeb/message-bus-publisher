<?php

namespace WakeOnWeb\MessageBusPublisher\Domain\Audit;

use Prooph\Common\Messaging\DomainMessage;
use WakeOnWeb\MessageBusPublisher\Domain\Gateway\GatewayResponse;

/**
 * AuditorInterface.
 *
 * @author Stephane PY <s.py@wakeonweb.com>
 */
interface AuditorInterface
{
    public function registerListenedMessage(DomainMessage $message, bool $routed);

    public function registerTargetedMessage(DomainMessage $message, string $targetId, GatewayResponse $gatewayResponse);
}
