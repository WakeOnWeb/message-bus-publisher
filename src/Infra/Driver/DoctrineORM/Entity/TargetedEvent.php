<?php

namespace WakeOnWeb\EventBusPublisher\Infra\Driver\DoctrineORM\Entity;

use Prooph\Common\Messaging\DomainEvent;

/**
 * TargetedEvent.
 *
 * @author Stephane PY <s.py@wakeonweb.com>
 */
class TargetedEvent
{
    protected $id;
    protected $target;
    protected $eventId;
    protected $eventName;
    protected $message;
    protected $createdAt;

    private function __construct(string $target, string $eventId, string $eventName, string $message)
    {
        $this->target = $target;
        $this->eventId = $eventId;
        $this->eventName = $eventName;
        $this->message = $message;
        $this->createdAt = new \DateTimeImmutable();
    }

    public static function createFromDomainEvent(DomainEvent $event, string $target)
    {
        return new static(
            $target,
            (string) $event->uuid(),
            $event->messageName(),
            json_encode($event->toArray())
        );
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTarget(): string
    {
        return $this->target;
    }

    public function getEventId(): string
    {
        return $this->eventId;
    }

    public function getEventName(): string
    {
        return $this->eventName;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }
}
