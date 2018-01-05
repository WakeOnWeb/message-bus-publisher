<?php

namespace WakeOnWeb\EventBusPublisher\Infra\Publishing;

use Prooph\Common\Event\DefaultActionEvent;
use Prooph\ServiceBus\MessageBus;
use Prooph\ServiceBus\Plugin\AbstractPlugin;
use WakeOnWeb\EventBusPublisher\Domain\Publishing\Delivery\DeliveryInterface;
use WakeOnWeb\EventBusPublisher\Domain\Router\EventRouterInterface;

/**
 * ProophEventBusPlugin.
 *
 * @uses \AbstractPlugin
 *
 * @author Stephane PY <s.py@wakeonweb.com>
 */
class ProophEventBusPlugin extends AbstractPlugin
{
    /** var EventRouterInterface */
    private $eventRouter;

    /** var DeliveryInterface */
    private $delivery;

    /**
     * @param EventRouterInterface $eventRouter eventRouter
     * @param DeliveryInterface    $delivery    delivery
     */
    public function __construct(EventRouterInterface $eventRouter, DeliveryInterface $delivery)
    {
        $this->eventRouter = $eventRouter;
        $this->delivery = $delivery;
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
        $message = $event->getParam(MessageBus::EVENT_PARAM_MESSAGE);
        $targetIds = $this->eventRouter->route($message);

        foreach ($targetIds as $targetId) {
            $this->delivery->deliver($message, $targetId);
        }
    }
}
