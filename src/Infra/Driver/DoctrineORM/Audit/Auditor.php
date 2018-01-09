<?php

namespace WakeOnWeb\EventBusPublisher\Infra\Driver\DoctrineORM\Audit;

use Doctrine\ORM\EntityManagerInterface;
use Prooph\Common\Messaging\DomainEvent;
use WakeOnWeb\EventBusPublisher\Domain\Audit\AuditorInterface;
use WakeOnWeb\EventBusPublisher\Domain\Gateway\GatewayResponse;
use WakeOnWeb\EventBusPublisher\Infra\Driver\DoctrineORM\Entity\ResponseAwareEventInterface;

class Auditor implements AuditorInterface
{
    private $entityManager;
    private $listenedEventClass;
    private $targetedEventClass;
    private $onlyRoutedEvents;

    public function __construct(EntityManagerInterface $entityManager, string $listenedEventClass, string $targetedEventClass, bool $onlyRoutedEvents = false)
    {
        $this->entityManager = $entityManager;
        $this->listenedEventClass = $listenedEventClass;
        $this->targetedEventClass = $targetedEventClass;
        $this->onlyRoutedEvents = $onlyRoutedEvents;
    }

    public function registerListenedEvent(DomainEvent $event, bool $routed)
    {
        if (false === $routed && true === $this->onlyRoutedEvents) {
            return;
        }

        $listenedEvent = call_user_func([$this->listenedEventClass, 'createFromDomainEvent'], $event);

        $this->entityManager->persist($listenedEvent);
        $this->entityManager->flush();
    }

    public function registerTargetedEvent(DomainEvent $event, string $targetId, GatewayResponse $gatewayResponse)
    {
        $targetedEvent = call_user_func([$this->targetedEventClass, 'createFromDomainEvent'], $event, $targetId);

        if ($targetedEvent instanceof ResponseAwareEventInterface) {
            $targetedEvent->setGatewayResponse($gatewayResponse);
        }

        $this->entityManager->persist($targetedEvent);
        $this->entityManager->flush();
    }
}
