<?php

namespace WakeOnWeb\EventBusPublisher\Domain\Event;

class DefaultEventIdentifierResolver implements EventIdentifierResolverInterface
{
    /**
     * {@inheritdoc}
     */
    public function resolve($event): string
    {
        if (is_object($event)) {
            //@todo implement an interface could return an event id.
            return get_class($event);
        }

        if (false === is_scalar($event)) {
            throw new \LogicException(sprintf('%s can resolve class or scalar events.', __CLASS__));
        }

        return $event;
    }
}
