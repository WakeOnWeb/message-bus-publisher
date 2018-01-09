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

    private $status;
    private $body;
    private $time;

    private function __construct(string $status, string $body = null, float $time = null)
    {
        $this->status = $status;
        $this->body = $body;
        $this->time = $time;
    }

    public static function createSuccessfulResponse(string $body = null, float $time = null)
    {
        return new self(static::SUCCESS, $body, $time);
    }

    public static function createFailureResponse(string $body = null, float $time = null)
    {
        return new self(static::FAILURE, $body, $time);
    }

    public function isSuccess(): bool
    {
        return $this->status === static::SUCCESS;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function getTime(): ?float
    {
        return $this->time;
    }

    public function withTime(float $time): GatewayResponse
    {
        return new self($this->status, $this->body, $time);
    }
}
