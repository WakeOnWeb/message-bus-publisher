<?php

namespace WakeOnWeb\MessageBusPublisher\Domain\Gateway;

/**
 * GatewayInterface.
 *
 * @author Stephane PY <s.py@wakeonweb.com>
 */
interface GatewayInterface
{
    public function send($message): GatewayResponse;
}
