<?php

namespace WakeOnWeb\MessageBusPublisher\Infra\Audit\PsrLogger;

use Prooph\Common\Messaging\DomainMessage;
use Psr\Log\LogLevel;
use Psr\Log\LoggerInterface;
use WakeOnWeb\MessageBusPublisher\Domain\Audit\AuditorInterface;
use WakeOnWeb\MessageBusPublisher\Domain\Gateway\GatewayResponse;

class Auditor implements AuditorInterface
{
    /** @var LoggerInterface */
    private $logger;

    /** @var bool */
    private $onlyRoutedMessages;

    /** @var string */
    private $level;

    public function __construct(LoggerInterface $logger, bool $onlyRoutedMessages = false, string $level = LogLevel::NOTICE)
    {
        $this->logger = $logger;
        $this->onlyRoutedMessages = $onlyRoutedMessages;
        $this->level = $level;
    }

    public function registerListenedMessage(DomainMessage $message, bool $routed)
    {
        if (false === $routed && true === $this->onlyRoutedMessages) {
            return;
        }

        $this->logger->log($this->level, '[wakoneweb.message_bus_publisher][listened_message] Message {message_name} #{message_id}, routed: {routed},  with message: {message}', [
            'message_name' => $message->messageName(),
            'message_id' => (string) $message->uuid(),
            'routed' => $routed ? 'yes' : 'no',
            'message' => json_encode($message->toArray()),
        ]);
    }

    public function registerTargetedMessage(DomainMessage $message, string $targetId, GatewayResponse $gatewayResponse)
    {
        $this->logger->log($this->level, '[wakoneweb.message_bus_publisher][targeted_message] Target {target}, Message {message_name} #{message_id} with message: {message}', [
            'target' => $targetId,
            'message_name' => $message->messageName(),
            'message_id' => (string) $message->uuid(),
            'message' => json_encode($message->toArray()),
        ]);

        $this->logger->log(LogLevel::DEBUG, '[wakoneweb.message_bus_publisher][targeted_message] Message #{message_id}, Response state: {response_state} in {response_time} second(s) with body: {response_body}', [
            'message_id' => (string) $message->uuid(),
            'response_state' => $gatewayResponse->isSuccess() ? 'succeed' : 'failed',
            'response_time' => $gatewayResponse->getTime() ?: '#',
            'response_body' => $gatewayResponse->getBody() ?: 'empty body',
        ]);
    }
}
