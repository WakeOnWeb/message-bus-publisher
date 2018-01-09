<?php

namespace WakeOnWeb\EventBusPublisher\Domain\Audit;

use WakeOnWeb\EventBusPublisher\Domain\Gateway\GatewayResponse;
use Prooph\Common\Messaging\DomainEvent;

class AuditorAggregator implements AuditorInterface
{
    private $auditors = [];

    public function __construct(array $auditors)
    {
        foreach ($auditors as $auditor) {
            $this->registerAuditor($auditor);
        }
    }

    public function registerListenedEvent(DomainEvent $event, bool $routed)
    {
        foreach ($this->auditors as $auditor) {
            $auditor->registerListenedEvent($event, $routed);
        }
    }

    public function registerTargetedEvent(DomainEvent $event, string $targetId, GatewayResponse $gatewayResponse)
    {
        foreach ($this->auditors as $auditor) {
            $auditor->registerTargetedEvent($event, $targetId, $gatewayResponse);
        }
    }

    private function registerAuditor(AuditorInterface $auditor)
    {
        $this->auditors[] = $auditor;
    }
}
