<?php

namespace WakeOnWeb\EventBusPublisher\Domain\Gateway;

/**
 * GatewayInterface.
 *
 * @author Stephane PY <s.py@wakeonweb.com>
 */
interface GatewayInterface
{
    public function send($message): GatewayResponse;
}
