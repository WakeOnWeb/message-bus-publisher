<?php

namespace WakeOnWeb\MessageBusPublisher\Infra\Normalizer;

use WakeOnWeb\MessageBusPublisher\Domain\Normalizer\NormalizerInterface;
use Prooph\Common\Messaging\DomainMessage;

/**
 * JsonNormalizer.
 *
 * @uses \AbstractNormalizer
 * @uses \NormalizerInterface
 *
 * @author Stephane PY <s.py@wakeonweb.com>
 */
class JsonNormalizer extends AbstractNormalizer implements NormalizerInterface
{
    private $options;

    /**
     * {@inheritdoc}
     */
    public function __construct(integer $options = null)
    {
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize(DomainMessage $message)
    {
        $message = $this->resetAsyncState($message);

        return json_encode($message->toArray(), $this->options);
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias(): string
    {
        return 'json';
    }
}
