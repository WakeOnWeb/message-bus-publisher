<?php

namespace WakeOnWeb\EventBusPublisher\Infra\Publishing;

use Prooph\Common\Event\DefaultActionEvent;
use Prooph\ServiceBus\MessageBus;
use Prooph\ServiceBus\Plugin\AbstractPlugin;
use WakeOnWeb\EventBusPublisher\Domain\Publishing\Delivery\DeliveryInterface;
use WakeOnWeb\EventBusPublisher\Domain\Router\EventRouterInterface;
use WakeOnWeb\EventBusPublisher\Domain\Audit\AuditorInterface;

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

    /** var AuditorInterface */
    private $auditor;

    /**
     * @param EventRouterInterface $eventRouter eventRouter
     * @param DeliveryInterface    $delivery    delivery
     * @param AuditorInterface     $auditor     auditor
     */
    public function __construct(EventRouterInterface $eventRouter, DeliveryInterface $delivery, AuditorInterface $auditor = null)
    {
        $this->eventRouter = $eventRouter;
        $this->delivery = $delivery;
        $this->auditor = $auditor;
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
        $event = $event->getParam(MessageBus::EVENT_PARAM_MESSAGE);
        $targetIds = $this->eventRouter->route($event);

        if ($this->auditor) {
            $this->auditor->registerListenedEvent($event, false === empty($targetIds));
        }

        foreach ($targetIds as $targetId) {
            $this->delivery->deliver($event, $targetId);
        }
    }
}
