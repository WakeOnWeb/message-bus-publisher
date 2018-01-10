<?php

namespace WakeOnWeb\MessageBusPublisher\Infra\Audit\DoctrineORM\Entity;

use WakeOnWeb\MessageBusPublisher\Domain\Gateway\GatewayResponse;

interface ResponseAwareMessageInterface
{
    public function setGatewayResponse(GatewayResponse $response);

    public function getGatewayResponse(): ?GatewayResponse;
}
