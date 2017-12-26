<?php

namespace WakeOnWeb\EventBusPublisher\Infra\Target;

use WakeOnWeb\EventBusPublisher\Domain\Exception\TargetNotFoundException;
use WakeOnWeb\EventBusPublisher\Domain\Target\Target;
use WakeOnWeb\EventBusPublisher\Domain\Target\TargetCollection;
use WakeOnWeb\EventBusPublisher\Domain\Target\TargetRepositoryInterface;

class InMemoryTargetRepository implements TargetRepositoryInterface
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
    public function findRequiredForIds(array $ids): TargetCollection
    {
        $coll = new TargetCollection();

        if (count($ids) === 0) {
            return $coll;
        }

        foreach ($ids as $id) {
            $coll->add($this->findRequired($id));
        }

        return $coll;
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
        $this->targets[$target->getName()] = $target;
    }
}
