<?php

namespace WakeOnWeb\MessageBusPublisher\Infra\Normalizer;

use Prooph\Common\Messaging\DomainMessage;

abstract class AbstractNormalizer
{
    public function resetAsyncState(DomainMessage $message): DomainMessage
    {
        $metadata = $message->metadata();

        if (false === array_key_exists('handled-async', $metadata)) {
            return $message;
        }

        $metadata['handled-async'] = false;

        return $message->withMetadata($metadata);
    }
}
