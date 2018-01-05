<?php

namespace WakeOnWeb\EventBusPublisher\Domain\Router;

/**
 * EventRouterInterface.
 *
 * @author Stephane PY <s.py@wakeonweb.com>
 */
interface EventRouterInterface
{
    public function route($event): array;
}
