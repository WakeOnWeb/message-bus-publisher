<?php

namespace WakeOnWeb\EventBusPublisher\Domain\Exception;

/**
 * NormalizerNotFoundException.
 *
 * @author Stephane PY <s.py@wakeonweb.com>
 */
class NormalizerNotFoundException extends \Exception
{
    public static function createFromId($id)
    {
        return new static(sprintf('Normalizer "%s" not found', $id));
    }
}
