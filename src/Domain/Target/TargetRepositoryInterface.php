<?php

namespace WakeOnWeb\EventBusPublisher\Domain\Target;

use WakeOnWeb\EventBusPublisher\Domain\Exception\TargetNotFoundException;

/**
 * TargetRepositoryInterface.
 *
 * @author Stephane PY <s.py@wakeonweb.com>
 */
interface TargetRepositoryInterface
{
    /**
     * @throws TargetNotFoundException
     */
    public function findRequired($id): Target;
}
