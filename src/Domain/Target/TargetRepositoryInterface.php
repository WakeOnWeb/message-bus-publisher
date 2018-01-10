<?php

namespace WakeOnWeb\MessageBusPublisher\Domain\Target;

use WakeOnWeb\MessageBusPublisher\Domain\Exception\TargetNotFoundException;

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
