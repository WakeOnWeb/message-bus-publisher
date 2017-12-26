<?php

namespace WakeOnWeb\EventBusPublisher\Domain\Exception;

/**
 * TargetNotFoundException
 *
 * @author Stephane PY <s.py@wakeonweb.com>
 */
class TargetNotFoundException extends \Exception
{
    public static function createFromId($id)
    {
        return new static(sprintf('Target "%s" not found',  $id));
    }
}
