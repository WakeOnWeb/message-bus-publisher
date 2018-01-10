<?php

namespace WakeOnWeb\MessageBusPublisher\Domain\Router;

/**
 * MessageRouterInterface.
 *
 * @author Stephane PY <s.py@wakeonweb.com>
 */
interface MessageRouterInterface
{
    public function route($message): array;
}
