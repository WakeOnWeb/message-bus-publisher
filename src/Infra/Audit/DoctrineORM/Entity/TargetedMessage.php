<?php

namespace WakeOnWeb\MessageBusPublisher\Infra\Audit\DoctrineORM\Entity;

use Prooph\Common\Messaging\DomainMessage;

/**
 * TargetedMessage.
 *
 * @author Stephane PY <s.py@wakeonweb.com>
 */
class TargetedMessage
{
    protected $id;
    protected $target;
    protected $messageId;
    protected $messageName;
    protected $message;
    protected $createdAt;

    private function __construct(string $target, string $messageId, string $messageName, string $message)
    {
        $this->target = $target;
        $this->messageId = $messageId;
        $this->messageName = $messageName;
        $this->message = $message;
        $this->createdAt = new \DateTimeImmutable();
    }

    public static function createFromDomainMessage(DomainMessage $message, string $target)
    {
        return new static(
            $target,
            (string) $message->uuid(),
            $message->messageName(),
            json_encode($message->toArray())
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

    public function getMessageId(): string
    {
        return $this->messageId;
    }

    public function getMessageName(): string
    {
        return $this->messageName;
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
