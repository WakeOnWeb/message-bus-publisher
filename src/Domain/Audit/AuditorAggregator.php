<?php

namespace WakeOnWeb\MessageBusPublisher\Domain\Audit;

use WakeOnWeb\MessageBusPublisher\Domain\Gateway\GatewayResponse;
use Prooph\Common\Messaging\DomainMessage;

class AuditorAggregator implements AuditorInterface
{
    private $auditors = [];

    public function __construct(array $auditors)
    {
        foreach ($auditors as $auditor) {
            $this->registerAuditor($auditor);
        }
    }

    public function registerListenedMessage(DomainMessage $message, bool $routed)
    {
        foreach ($this->auditors as $auditor) {
            $auditor->registerListenedMessage($message, $routed);
        }
    }

    public function registerTargetedMessage(DomainMessage $message, string $targetId, GatewayResponse $gatewayResponse)
    {
        foreach ($this->auditors as $auditor) {
            $auditor->registerTargetedMessage($message, $targetId, $gatewayResponse);
        }
    }

    private function registerAuditor(AuditorInterface $auditor)
    {
        $this->auditors[] = $auditor;
    }
}
