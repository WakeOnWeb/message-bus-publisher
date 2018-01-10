<?php

namespace WakeOnWeb\MessageBusPublisher\Infra\Audit\DoctrineORM\Entity;

use WakeOnWeb\MessageBusPublisher\Domain\Gateway\GatewayResponse;

/**
 * TargetedMessage.
 *
 * @author Stephane PY <s.py@wakeonweb.com>
 */
class TargetedMessageWithResponse extends TargetedMessage implements ResponseAwareMessageInterface
{
    protected $responseSucceed;
    protected $responseTime;
    protected $responseBody;

    public function setGatewayResponse(GatewayResponse $response)
    {
        $this->responseSucceed = $response->isSuccess();
        $this->responseTime = $response->getTime();
        $this->responseBody = $response->getBody();
    }

    public function getGatewayResponse(): ?GatewayResponse
    {
        if (true === $this->responseSucceed) {
            return GatewayResponse::createSuccessfulResponse($this->responseBody, $this->responseTime);
        } elseif (false === $this->responseSucceed) {
            return GatewayResponse::createFailureResponse($this->responseBody, $this->responseTime);
        }

        return $this->gatewayResponse;
    }
}
