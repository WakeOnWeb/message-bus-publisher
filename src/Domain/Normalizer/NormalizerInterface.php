<?php

namespace WakeOnWeb\EventBusPublisher\Domain\Normalizer;

use Prooph\Common\Messaging\DomainEvent;

/**
 * NormalizerInterface.
 *
 * @author Stephane PY <s.py@wakeonweb.com>
 */
interface NormalizerInterface
{
    /**
     *  Normalizer DomainEvent
     *  We can't ensure returned data is a string
     *  It'll depend on targets.
     */
    public function normalize(DomainEvent $event);

    /**
     *  Which alias to be configured.
     */
    public function getAlias(): string;
}
