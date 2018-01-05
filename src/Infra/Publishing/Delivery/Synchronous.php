<?php

namespace WakeOnWeb\EventBusPublisher\Infra\Publishing\Delivery;

use Prooph\Common\Messaging\DomainEvent;
use WakeOnWeb\EventBusPublisher\Domain\Publishing\Delivery\DeliveryInterface;
use WakeOnWeb\EventBusPublisher\Domain\Target\TargetRepositoryInterface;
use WakeOnWeb\EventBusPublisher\Domain\Normalizer\NormalizerRepositoryInterface;
use WakeOnWeb\EventBusPublisher\Domain\Gateway\GatewayFactoryInterface;

class Synchronous implements DeliveryInterface
{
    /** @var TargetRepositoryInterface; */
    private $targetRepository;

    /** @var NormalizerRepositoryInterface; */
    private $normalizerRepository;

    /** @var GatewayFactoryInterface ; */
    private $gatewayFactory;

    public function __construct(
        TargetRepositoryInterface $targetRepository,
        NormalizerRepositoryInterface $normalizerRepository,
        GatewayFactoryInterface $gatewayFactory
    ) {
        $this->targetRepository = $targetRepository;
        $this->normalizerRepository = $normalizerRepository;
        $this->gatewayFactory = $gatewayFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function deliver(DomainEvent $event, string $targetId): void
    {
        $target = $this->targetRepository->findRequired($targetId);
        $normalizer = $this->normalizerRepository->find($target->getNormalizer());

        $normalizedData = $normalizer ? $normalizer->normalize($event) : $event;

        $this->gatewayFactory
            ->createFromDefinition($target->getGatewayDefinition())
            ->send($normalizedData);
    }
}
