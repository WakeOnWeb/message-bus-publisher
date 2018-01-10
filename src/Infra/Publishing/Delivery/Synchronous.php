<?php

namespace WakeOnWeb\EventBusPublisher\Infra\Publishing\Delivery;

use Prooph\Common\Messaging\DomainEvent;
use WakeOnWeb\EventBusPublisher\Domain\Audit\AuditorInterface;
use WakeOnWeb\EventBusPublisher\Domain\Gateway\GatewayFactoryInterface;
use WakeOnWeb\EventBusPublisher\Domain\Normalizer\NormalizerRepositoryInterface;
use WakeOnWeb\EventBusPublisher\Domain\Publishing\Delivery\DeliveryInterface;
use WakeOnWeb\EventBusPublisher\Domain\Target\TargetRepositoryInterface;

class Synchronous implements DeliveryInterface
{
    /** @var TargetRepositoryInterface; */
    private $targetRepository;

    /** @var NormalizerRepositoryInterface; */
    private $normalizerRepository;

    /** @var GatewayFactoryInterface ; */
    private $gatewayFactory;

    /** var AuditorInterface */
    private $auditor;

    public function __construct(
        TargetRepositoryInterface $targetRepository,
        NormalizerRepositoryInterface $normalizerRepository,
        GatewayFactoryInterface $gatewayFactory,
        AuditorInterface $auditor = null
    ) {
        $this->targetRepository = $targetRepository;
        $this->normalizerRepository = $normalizerRepository;
        $this->gatewayFactory = $gatewayFactory;
        $this->auditor = $auditor;
    }

    /**
     * {@inheritdoc}
     */
    public function deliver(DomainEvent $event, string $targetId): void
    {
        $target = $this->targetRepository->findRequired($targetId);
        $normalizer = $this->normalizerRepository->find($target->getNormalizer());

        $normalizedData = $normalizer ? $normalizer->normalize($event) : $event;

        $beginTimer = microtime(true);
        $response = $this->gatewayFactory
            ->createFromDefinition($target->getGatewayDefinition())
            ->send($normalizedData);

        $response = $response->withTime(microtime(true) - $beginTimer);

        if ($this->auditor) {
            $this->auditor->registerTargetedEvent($event, $targetId, $response);
        }
    }
}
