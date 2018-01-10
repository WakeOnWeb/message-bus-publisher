<?php

namespace WakeOnWeb\MessageBusPublisher\Infra\Audit\DoctrineORM;

use Doctrine\ORM\EntityManagerInterface;
use Prooph\Common\Messaging\DomainMessage;
use WakeOnWeb\MessageBusPublisher\Domain\Audit\AuditorInterface;
use WakeOnWeb\MessageBusPublisher\Domain\Gateway\GatewayResponse;
use WakeOnWeb\MessageBusPublisher\Infra\Audit\DoctrineORM\Entity\ResponseAwareMessageInterface;

class Auditor implements AuditorInterface
{
    private $entityManager;
    private $listenedMessageClass;
    private $targetedMessageClass;
    private $onlyRoutedMessages;

    public function __construct(EntityManagerInterface $entityManager, string $listenedMessageClass, string $targetedMessageClass, bool $onlyRoutedMessages = false)
    {
        $this->entityManager = $entityManager;
        $this->listenedMessageClass = $listenedMessageClass;
        $this->targetedMessageClass = $targetedMessageClass;
        $this->onlyRoutedMessages = $onlyRoutedMessages;
    }

    public function registerListenedMessage(DomainMessage $message, bool $routed)
    {
        if (false === $routed && true === $this->onlyRoutedMessages) {
            return;
        }

        $listenedMessage = call_user_func([$this->listenedMessageClass, 'createFromDomainMessage'], $message);

        $this->entityManager->persist($listenedMessage);
        $this->entityManager->flush();
    }

    public function registerTargetedMessage(DomainMessage $message, string $targetId, GatewayResponse $gatewayResponse)
    {
        $targetedMessage = call_user_func([$this->targetedMessageClass, 'createFromDomainMessage'], $message, $targetId);

        if ($targetedMessage instanceof ResponseAwareMessageInterface) {
            $targetedMessage->setGatewayResponse($gatewayResponse);
        }

        $this->entityManager->persist($targetedMessage);
        $this->entityManager->flush();
    }
}
