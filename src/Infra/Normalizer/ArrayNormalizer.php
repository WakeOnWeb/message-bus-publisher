<?php

namespace WakeOnWeb\MessageBusPublisher\Infra\Normalizer;

use WakeOnWeb\MessageBusPublisher\Domain\Normalizer\NormalizerInterface;
use Prooph\Common\Messaging\DomainMessage;

/**
 * ArrayNormalizer.
 *
 * @uses \AbstractNormalizer
 * @uses \NormalizerInterface
 *
 * @author Stephane PY <s.py@wakeonweb.com>
 */
class ArrayNormalizer extends AbstractNormalizer implements NormalizerInterface
{
    /**
     * {@inheritdoc}
     */
    public function normalize(DomainMessage $message)
    {
        $message = $this->resetAsyncState($message);

        return $message->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias(): string
    {
        return 'array';
    }
}
