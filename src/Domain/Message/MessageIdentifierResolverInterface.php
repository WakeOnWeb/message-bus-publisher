<?php

namespace WakeOnWeb\MessageBusPublisher\Domain\Message;

interface MessageIdentifierResolverInterface
{
    /**
     * resolve message id from an object, a string ...
     */
    public function resolve($message): string;
}
