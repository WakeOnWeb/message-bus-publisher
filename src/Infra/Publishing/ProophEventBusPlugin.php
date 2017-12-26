<?php

namespace WakeOnWeb\EventBusPublisher\Infra\Publishing;

use Bernard\Message\PlainMessage;
use Bernard\Producer as BernardProducer;
use Prooph\Common\Event\DefaultActionEvent;
use Prooph\ServiceBus\MessageBus;
use Prooph\ServiceBus\Plugin\AbstractPlugin;
use WakeOnWeb\EventBusPublisher\Domain\Router\EventRouterInterface;
use WakeOnWeb\EventBusPublisher\Infra\Queue\BernardReceiver;

class ProophEventBusPlugin extends AbstractPlugin
{
    private $eventRouter;
    private $bernardProducer;
    private $queueName;

    public function __construct(EventRouterInterface $eventRouter, BernardProducer $bernardProducer, string $queueName)
    {
        $this->eventRouter = $eventRouter;
        $this->bernardProducer = $bernardProducer;
        $this->queueName = $queueName;
    }

    public function attachToMessageBus(MessageBus $messageBus): void
    {
        $this->listenerHandlers[] = $messageBus->attach(
            MessageBus::EVENT_DISPATCH,
            [$this, 'onRouteMessage'],
            MessageBus::PRIORITY_ROUTE
        );
    }

    public function onRouteMessage(DefaultActionEvent $event)
    {
        $message   = $event->getParam(MessageBus::EVENT_PARAM_MESSAGE);
        $targetIds = $this->eventRouter->route($message);

        foreach ($targetIds as $targetId) {
            $bernardMessage = new PlainMessage(BernardReceiver::MESSAGE_NAME, [
                'target' => $targetId,
                'domain_event' => serialize($message),
            ]);

            $this->bernardProducer->produce($bernardMessage, $this->guessQueueName($targetId));
        }
    }

    private function guessQueueName($targetName)
    {
        return str_replace(['{target}'], [$targetName], $this->queueName);
    }
}
