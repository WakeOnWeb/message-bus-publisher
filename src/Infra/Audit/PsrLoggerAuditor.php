<?php

namespace WakeOnWeb\EventBusPublisher\Infra\Audit;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use WakeOnWeb\EventBusPublisher\Domain\Audit\AuditorInterface;
use WakeOnWeb\EventBusPublisher\Domain\Gateway\GatewayResponse;
use Prooph\Common\Messaging\DomainEvent;

class PsrLoggerAuditor implements AuditorInterface
{
    /** @var LoggerInterface */
    private $logger;

    /** @var bool */
    private $onlyRoutedEvents;

    /** @var string */
    private $level;

    public function __construct(LoggerInterface $logger, bool $onlyRoutedEvents = false, string $level = LogLevel::NOTICE)
    {
        $this->logger = $logger;
        $this->onlyRoutedEvents = $onlyRoutedEvents;
        $this->level = $level;
    }

    public function registerListenedEvent(DomainEvent $event, bool $routed)
    {
        if (false === $routed && true === $this->onlyRoutedEvents) {
            return;
        }

        $this->logger->log($this->level, '[wakoneweb.event_bus_publisher][listened_event] Event {event_name} #{event_id}, routed: {routed},  with message: {message}', [
            'event_name' => $event->messageName(),
            'event_id' => (string) $event->uuid(),
            'routed' => $routed ? 'yes' : 'no',
            'message' => json_encode($event->toArray()),
        ]);
    }

    public function registerTargetedEvent(DomainEvent $event, string $targetId, GatewayResponse $gatewayResponse)
    {
        $this->logger->log($this->level, '[wakoneweb.event_bus_publisher][targeted_event] Target {target}, Event {event_name} #{event_id} with message: {message}', [
            'target' => $targetId,
            'event_name' => $event->messageName(),
            'event_id' => (string) $event->uuid(),
            'message' => json_encode($event->toArray()),
        ]);

        $this->logger->log(LogLevel::DEBUG, '[wakoneweb.event_bus_publisher][targeted_event] Event #{event_id}, Response state: {response_state} in {response_time} second(s) with body: {response_body}', [
            'event_id' => (string) $event->uuid(),
            'response_state' => $gatewayResponse->isSuccess() ? 'succeed' : 'failed',
            'response_time' => $gatewayResponse->getTime() ?: '#',
            'response_body' => $gatewayResponse->getBody() ?: 'empty body',
        ]);
    }
}
