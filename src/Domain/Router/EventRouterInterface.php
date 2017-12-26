<?php

namespace WakeOnWeb\EventBusPublisher\Domain\Router;

use WakeOnWeb\EventBusPublisher\Domain\Target\TargetCollection;

/**
 * EventRouterInterface
 *
 * @author Stephane PY <s.py@wakeonweb.com>
 */
interface EventRouterInterface
{
    public function route($event): array;
}
