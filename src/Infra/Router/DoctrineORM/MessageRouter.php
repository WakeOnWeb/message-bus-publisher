<?php

namespace WakeOnWeb\MessageBusPublisher\Infra\Router\DoctrineORM;

use Doctrine\ORM\EntityManagerInterface;
use WakeOnWeb\MessageBusPublisher\Domain\Message\DefaultMessageIdentifierResolver;
use WakeOnWeb\MessageBusPublisher\Domain\Message\MessageIdentifierResolverInterface;
use WakeOnWeb\MessageBusPublisher\Domain\Router\MessageRouterInterface;

/**
 * MessageRouter.
 *
 * @uses \MessageRouterInterface
 *
 * @author Stephane PY <s.py@wakeonweb.com>
 */
class MessageRouter implements MessageRouterInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var string */
    private $routeClass;

    /** @var MessageIdentifierResolverInterface */
    private $messageIdentifierResolver;

    /**
     * @param EntityManagerInterface           $entityManager           entityManager
     * @param string                           $routeClass              routeClass
     * @param MessageIdentifierResolverInterface $messageIdentifierResolver messageIdentifierResolver
     */
    public function __construct(EntityManagerInterface $entityManager, string $routeClass, MessageIdentifierResolverInterface $messageIdentifierResolver = null)
    {
        $this->entityManager = $entityManager;
        $this->routeClass = $routeClass;
        $this->messageIdentifierResolver = $messageIdentifierResolver ?: new DefaultMessageIdentifierResolver();
    }

    /**
     * {@inheritdoc}
     */
    public function route($message): array
    {
        return $this->getTargetIdsListeningMessage(
            $this->messageIdentifierResolver->resolve($message)
        );
    }

    private function getTargetIdsListeningMessage(string $message)
    {
        $results = $this->entityManager->getRepository($this->routeClass)
            ->createQueryBuilder('r')
            ->select('IDENTITY(r.target) as target')
            ->where('r.messageName = :message')
            ->setParameter('message', $message)
            ->getQuery()
            ->getScalarResult();

        return array_column($results, 'target');
    }
}
