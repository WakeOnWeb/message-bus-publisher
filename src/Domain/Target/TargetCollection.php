<?php

namespace WakeOnWeb\EventBusPublisher\Domain\Target;

/**
 * TargetCollection
 *
 * @author Stephane PY <s.py@wakeonweb.com>
 */
class TargetCollection implements \IteratorAggregate, \Countable
{
    private $items = [];

    public function __construct(array $items = [])
    {
        foreach ($items as $item) {
            $this->add($item);
        }
    }

    public function add(Target $target): void
    {
        $this->items[] = $target;
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->items);
    }
}
