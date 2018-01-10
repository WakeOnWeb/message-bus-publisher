<?php

namespace WakeOnWeb\MessageBusPublisher\Infra\Router\DoctrineORM\Entity;

/**
 * Route.
 *
 * @author Stephane PY <s.py@wakeonweb.com>
 */
class Route
{
    /** @var int */
    private $id;

    /** @var string */
    private $messageName;

    /** @var Target */
    private $target;

    public function __construct(string $messageName, Target $target)
    {
        $this->messageName = $messageName;
        $this->target = $target;
    }

    public function getId(): integer
    {
        return $this->id;
    }

    public function getMessageName(): string
    {
        return $this->messageName;
    }

    public function getTarget(): Target
    {
        return $this->target;
    }
}
