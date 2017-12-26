<?php

namespace WakeOnWeb\EventBusPublisher\Domain\Target;

use WakeOnWeb\EventBusPublisher\Domain\Gateway\GatewayInterface;
use WakeOnWeb\EventBusPublisher\Domain\Normalizer\NormalizerInterface;

/**
 * Target.
 *
 * @author Stephane PY <s.py@wakeonweb.com>
 */
class Target
{
    private $name;
    private $gateway;
    private $normalizer;

    public function __construct(string $name, GatewayInterface $gateway, NormalizerInterface $normalizer = null)
    {
        $this->name = $name;
        $this->gateway = $gateway;
        $this->normalizer = $normalizer;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getGateway(): GatewayInterface
    {
        return $this->gateway;
    }

    public function hasNormalizer(): boolean
    {
        return $this->normalizer !== null;
    }

    public function getNormalizer(): ?NormalizerInterface
    {
        return $this->normalizer;
    }
}
