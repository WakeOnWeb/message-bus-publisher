<?php

namespace WakeOnWeb\EventBusPublisher\Infra\Normalizer;

use WakeOnWeb\EventBusPublisher\Domain\Normalizer\NormalizerInterface;
use Prooph\Common\Messaging\DomainEvent;

abstract class AbstractNormalizer
{
    public function resetAsyncState(DomainEvent $event): DomainEvent
    {
        $metadata = $event->metadata();

        if (false === array_key_exists('handled-async', $metadata)) {
            return $event;
        }

        $metadata['handled-async'] = false;

        return $event->withMetadata($metadata);
    }
}
