<?php

namespace WakeOnWeb\EventBusPublisher\Infra\Publishing\Delivery;

use Prooph\Common\Messaging\DomainEvent;
use WakeOnWeb\EventBusPublisher\Domain\Publishing\Delivery\DeliveryInterface;
use WakeOnWeb\EventBusPublisher\Domain\Target\TargetRepositoryInterface;

class Synchronous implements DeliveryInterface
{
    /** @var TargetRepositoryInterface; */
    private $targetRepository;

    /**
     * @param TargetRepositoryInterface $targetRepository targetRepository
     */
    public function __construct(TargetRepositoryInterface $targetRepository)
    {
        $this->targetRepository = $targetRepository;
    }

    /**
     * @{inheritdoc}
     */
    public function deliver(DomainEvent $event, string $targetId): void
    {
        $target = $this->targetRepository->findRequired($targetId);

        $normalizedData = $target->getNormalizer()->normalize($event);

        $target->getGateway()->send($normalizedData);
    }
}
