<?php

namespace WakeOnWeb\EventBusPublisher\Domain\Event;

interface EventIdentifierResolverInterface
{
    /**
     * resolve event id from an object, a string ...
     */
    public function resolve($event): string;
}
