<?php

namespace WakeOnWeb\EventBusPublisher\Infra\Driver\DoctrineORM;

use Doctrine\ORM\EntityManagerInterface;
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
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var string */
    private $routeClass;

    /** @var EventIdentifierResolverInterface */
    private $eventIdentifierResolver;

    /**
     * @param EntityManagerInterface           $entityManager           entityManager
     * @param string                           $routeClass              routeClass
     * @param EventIdentifierResolverInterface $eventIdentifierResolver eventIdentifierResolver
     */
    public function __construct(EntityManagerInterface $entityManager, string $routeClass, EventIdentifierResolverInterface $eventIdentifierResolver = null)
    {
        $this->entityManager = $entityManager;
        $this->routeClass = $routeClass;
        $this->eventIdentifierResolver = $eventIdentifierResolver ?: new DefaultEventIdentifierResolver();
    }

    /**
     * {@inheritdoc}
     */
    public function route($event): array
    {
        return $this->getTargetIdsListeningEvent(
            $this->eventIdentifierResolver->resolve($event)
        );
    }

    private function getTargetIdsListeningEvent(string $event)
    {
        $results = $this->entityManager->getRepository($this->routeClass)
            ->createQueryBuilder('r')
            ->select('IDENTITY(r.target) as target')
            ->where('r.eventName = :event')
            ->setParameter('event', $event)
            ->getQuery()
            ->getScalarResult();

        return array_column($results, 'target');
    }
}
