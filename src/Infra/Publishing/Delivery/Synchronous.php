<?php

namespace WakeOnWeb\MessageBusPublisher\Infra\Publishing\Delivery;

use Prooph\Common\Messaging\DomainMessage;
use WakeOnWeb\MessageBusPublisher\Domain\Audit\AuditorInterface;
use WakeOnWeb\MessageBusPublisher\Domain\Gateway\GatewayFactoryInterface;
use WakeOnWeb\MessageBusPublisher\Domain\Normalizer\NormalizerRepositoryInterface;
use WakeOnWeb\MessageBusPublisher\Domain\Publishing\Delivery\DeliveryInterface;
use WakeOnWeb\MessageBusPublisher\Domain\Target\TargetRepositoryInterface;

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
    public function deliver(DomainMessage $message, string $targetId): void
    {
        $target = $this->targetRepository->findRequired($targetId);
        $normalizer = $this->normalizerRepository->find($target->getNormalizer());

        $normalizedData = $normalizer ? $normalizer->normalize($message) : $message;

        $beginTimer = microtime(true);
        $response = $this->gatewayFactory
            ->createFromDefinition($target->getGatewayDefinition())
            ->send($normalizedData);

        $response = $response->withTime(microtime(true) - $beginTimer);

        if ($this->auditor) {
            $this->auditor->registerTargetedMessage($message, $targetId, $response);
        }
    }
}
