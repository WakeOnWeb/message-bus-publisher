<?php

namespace WakeOnWeb\EventBusPublisher\Infra\Router\InMemory;

use WakeOnWeb\EventBusPublisher\Domain\Event\DefaultEventIdentifierResolver;
use WakeOnWeb\EventBusPublisher\Domain\Event\EventIdentifierResolverInterface;
use WakeOnWeb\EventBusPublisher\Domain\Router\EventRouterInterface;

/**
 * EventRouter.
 *
 * @uses \EventRouterInterface
 *
 * @author Stephane PY <s.py@wakeonweb.com>
 */
class EventRouter implements EventRouterInterface
{
    /** var array */
    private $routes = [];

    /** var EventIdentifierResolverInterface */
    private $eventIdentifierResolver;

    /**
     * @param EventIdentifierResolverInterface $eventIdentifierResolver eventIdentifierResolver
     */
    public function __construct(EventIdentifierResolverInterface $eventIdentifierResolver = null)
    {
        $this->eventIdentifierResolver = $eventIdentifierResolver ?: new DefaultEventIdentifierResolver();
    }

    /**
     * @param string $event  event
     * @param string $target target
     */
    public function addRoute(string $event, string $target): void
    {
        $this->routes[$event][] = $target;
    }

    /**
     * {@inheritdoc}
     */
    public function route($event): array
    {
        $eventId = $this->eventIdentifierResolver->resolve($event);

        if (false === array_key_exists($eventId, $this->routes)) {
            return [];
        }

        return $this->routes[$eventId];
    }
}
