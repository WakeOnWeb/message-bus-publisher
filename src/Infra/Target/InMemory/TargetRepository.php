<?php

namespace WakeOnWeb\EventBusPublisher\Infra\Target\InMemory;

use WakeOnWeb\EventBusPublisher\Domain\Exception\TargetNotFoundException;
use WakeOnWeb\EventBusPublisher\Domain\Target\Target;
use WakeOnWeb\EventBusPublisher\Domain\Target\TargetRepositoryInterface;

class TargetRepository implements TargetRepositoryInterface
{
    /** var array */
    private $targets;

    /**
     * @param array $targets targets
     */
    public function __construct(array $targets = [])
    {
        foreach ($targets as $target) {
            $this->addTarget($target);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function findRequired($id): Target
    {
        if (false === array_key_exists($id, $this->targets)) {
            throw TargetNotFoundException::createFromId($id);
        }

        return $this->targets[$id];
    }

    private function addTarget(Target $target)
    {
        $this->targets[$target->getId()] = $target;
    }
}
