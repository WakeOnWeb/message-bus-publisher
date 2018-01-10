<?php

namespace WakeOnWeb\MessageBusPublisher\Infra\Publishing;

use Prooph\Common\Event\DefaultActionEvent;
use Prooph\ServiceBus\MessageBus;
use Prooph\ServiceBus\Plugin\AbstractPlugin;
use WakeOnWeb\MessageBusPublisher\Domain\Audit\AuditorInterface;
use WakeOnWeb\MessageBusPublisher\Domain\Publishing\Delivery\DeliveryInterface;
use WakeOnWeb\MessageBusPublisher\Domain\Router\MessageRouterInterface;

/**
 * ProophMessageBusPlugin.
 *
 * @uses \AbstractPlugin
 *
 * @author Stephane PY <s.py@wakeonweb.com>
 */
class ProophMessageBusPlugin extends AbstractPlugin
{
    /** var MessageRouterInterface */
    private $messageRouter;

    /** var DeliveryInterface */
    private $delivery;

    /** var AuditorInterface */
    private $auditor;

    /**
     * @param MessageRouterInterface $messageRouter messageRouter
     * @param DeliveryInterface    $delivery    delivery
     * @param AuditorInterface     $auditor     auditor
     */
    public function __construct(MessageRouterInterface $messageRouter, DeliveryInterface $delivery, AuditorInterface $auditor = null)
    {
        $this->messageRouter = $messageRouter;
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
        $message = $event->getParam(MessageBus::EVENT_PARAM_MESSAGE);
        $targetIds = $this->messageRouter->route($message);

        if ($this->auditor) {
            $this->auditor->registerListenedMessage($message, false === empty($targetIds));
        }

        foreach ($targetIds as $targetId) {
            $this->delivery->deliver($message, $targetId);
        }
    }
}
