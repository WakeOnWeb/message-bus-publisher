<?php

namespace WakeOnWeb\MessageBusPublisher\Domain\Gateway;

use WakeOnWeb\MessageBusPublisher\Domain\Gateway\Definition\GatewayDefinitionInterface;

/**
 * GatewayFactoryInterface.
 *
 * @author Stephane PY <s.py@wakeonweb.com>
 */
interface GatewayFactoryInterface
{
    public function createFromDefinition(GatewayDefinitionInterface $gatewayDefinition): GatewayInterface;
}
