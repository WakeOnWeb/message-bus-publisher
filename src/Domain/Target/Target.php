<?php

namespace WakeOnWeb\MessageBusPublisher\Domain\Target;

use WakeOnWeb\MessageBusPublisher\Domain\Gateway\Definition\GatewayDefinitionInterface;

/**
 * Target.
 *
 * @author Stephane PY <s.py@wakeonweb.com>
 */
class Target
{
    protected $id;
    protected $gatewayDefinition;
    protected $normalizer;

    public function __construct(string $id, GatewayDefinitionInterface $gatewayDefinition, string $normalizer = null)
    {
        $this->id = $id;
        $this->gatewayDefinition = $gatewayDefinition;
        $this->normalizer = $normalizer;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getGatewayDefinition(): GatewayDefinitionInterface
    {
        return $this->gatewayDefinition;
    }

    public function hasNormalizer(): boolean
    {
        return null !== $this->normalizer;
    }

    public function getNormalizer(): ?string
    {
        return $this->normalizer;
    }
}
