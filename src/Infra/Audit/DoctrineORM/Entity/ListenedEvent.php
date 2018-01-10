<?php

namespace WakeOnWeb\EventBusPublisher\Infra\Audit\DoctrineORM\Entity;

use Prooph\Common\Messaging\DomainEvent;

/**
 * ListenedEvent.
 *
 * @author Stephane PY <s.py@wakeonweb.com>
 */
class ListenedEvent
{
    protected $id;
    protected $eventId;
    protected $eventName;
    protected $message;
    protected $createdAt;

    private function __construct(string $eventId, string $eventName, string $message)
    {
        $this->eventId = $eventId;
        $this->eventName = $eventName;
        $this->message = $message;
        $this->createdAt = new \DateTimeImmutable();
    }

    public static function createFromDomainEvent(DomainEvent $event)
    {
        return new static(
            (string) $event->uuid(),
            $event->messageName(),
            json_encode($event->toArray())
        );
    }

    public function getId(): int
    {
        return $this->id;
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
