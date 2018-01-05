<?php

namespace WakeOnWeb\EventBusPublisher\Domain\Gateway;

/**
 * GatewayResponse.
 *
 * @author Stephane PY <s.py@wakeonweb.com>
 */
final class GatewayResponse
{
    const SUCCESS = 'success';
    const FAILURE = 'failure';

    private function __construct(string $status, string $body = null)
    {
        $this->status = $status;
        $this->body = $body;
    }

    public static function createSuccessfulResponse(string $body = null)
    {
        return new self(static::SUCCESS, $body);
    }

    public static function createFailureResponse(string $body = null)
    {
        return new self(static::FAILURE, $body);
    }

    public function isSuccess()
    {
        return $this->status === static::SUCCESS;
    }

    public function getBody()
    {
        return $this->body;
    }
}
