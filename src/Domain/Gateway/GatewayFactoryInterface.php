<?php

namespace WakeOnWeb\EventBusPublisher\Domain\Gateway;

use WakeOnWeb\EventBusPublisher\Domain\Gateway\Definition\GatewayDefinitionInterface;

/**
 * GatewayFactoryInterface.
 *
 * @author Stephane PY <s.py@wakeonweb.com>
 */
interface GatewayFactoryInterface
{
    public function createFromDefinition(GatewayDefinitionInterface $gatewayDefinition): GatewayInterface;
}
