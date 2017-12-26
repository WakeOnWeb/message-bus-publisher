<?php

namespace WakeOnWeb\EventBusPublisher\Infra\Normalizer;

use WakeOnWeb\EventBusPublisher\Domain\Normalizer\NormalizerInterface;
use Prooph\Common\Messaging\DomainEvent;

/**
 * ArrayNormalizer
 *
 * @uses AbstractNormalizer
 * @uses NormalizerInterface
 * @author Stephane PY <s.py@wakeonweb.com>
 */
class ArrayNormalizer extends AbstractNormalizer implements NormalizerInterface
{
    /**
     * @{inheritdoc}
     */
    public function normalize(DomainEvent $event)
    {
        $event = $this->resetAsyncState($event);

        return $event->toArray();
    }
}
