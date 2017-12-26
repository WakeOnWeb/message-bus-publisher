<?php

namespace WakeOnWeb\EventBusPublisher\Infra\Router;

use WakeOnWeb\EventBusPublisher\Domain\Event\DefaultEventIdentifierResolver;
use WakeOnWeb\EventBusPublisher\Domain\Event\EventIdentifierResolverInterface;
use WakeOnWeb\EventBusPublisher\Domain\Router\EventRouterInterface;
use WakeOnWeb\EventBusPublisher\Domain\Target\TargetCollection;
use WakeOnWeb\EventBusPublisher\Domain\Target\TargetRepositoryInterface;

/**
 * InMemoryEventRouter
 *
 * @uses EventRouterInterface
 * @author Stephane PY <s.py@wakeonweb.com>
 */
class InMemoryEventRouter implements EventRouterInterface
{
    /** var array */
    private $routes = [];

    /** var TargetRepositoryInterface */
    private $targetRepository;

    /** var EventIdentifierResolverInterface */
    private $eventIdentifierResolver;

    /**
     * @param TargetRepositoryInterface $targetRepository targetRepository
     * @param EventIdentifierResolverInterface $eventIdentifierResolver eventIdentifierResolver
     */
    public function __construct(TargetRepositoryInterface $targetRepository, EventIdentifierResolverInterface $eventIdentifierResolver = null)
    {
        $this->targetRepository = $targetRepository;
        $this->eventIdentifierResolver = $eventIdentifierResolver ?: new DefaultEventIdentifierResolver();
    }

    /**
     * @param string $event event
     * @param string $target target
     */
    public function addRoute(string $event, string $target): void
    {
        $this->routes[$event][] = $target;
    }

    /**
     * @{inheritdoc}
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
