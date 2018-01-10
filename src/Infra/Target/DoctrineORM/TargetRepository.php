<?php

namespace WakeOnWeb\MessageBusPublisher\Infra\Target\DoctrineORM;

use Doctrine\ORM\EntityManagerInterface;
use WakeOnWeb\MessageBusPublisher\Domain\Exception\TargetNotFoundException;
use WakeOnWeb\MessageBusPublisher\Domain\Target\Target;
use WakeOnWeb\MessageBusPublisher\Domain\Target\TargetRepositoryInterface;

class TargetRepository implements TargetRepositoryInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var string */
    private $targetClass;

    /**
     * @param EntityManagerInterface $entityManager entityManager
     * @param string                 $targetClass   targetClass
     */
    public function __construct(EntityManagerInterface $entityManager, string $targetClass)
    {
        $this->entityManager = $entityManager;
        $this->targetClass = $targetClass;
    }

    /**
     * {@inheritdoc}
     */
    public function findRequired($id): Target
    {
        $target = $this->getRepository()->find($id);

        if (null === $target) {
            throw TargetNotFoundException::createFromId($id);
        }

        return $target;
    }

    private function getRepository()
    {
        return $this->entityManager->getRepository($this->targetClass);
    }
}
