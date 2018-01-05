<?php

namespace WakeOnWeb\EventBusPublisher\Infra\Driver\DoctrineORM\Entity;

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
    private $eventName;

    /** @var Target */
    private $target;

    public function __construct(string $eventName, Target $target)
    {
        $this->eventName = $eventName;
        $this->target = $target;
    }

    public function getId(): integer
    {
        return $this->id;
    }

    public function getEventName(): string
    {
        return $this->eventName;
    }

    public function getTarget(): Target
    {
        return $this->target;
    }
}
