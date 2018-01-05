<?php

namespace WakeOnWeb\EventBusPublisher\Infra\Normalizer;

use WakeOnWeb\EventBusPublisher\Domain\Normalizer\NormalizerInterface;
use Prooph\Common\Messaging\DomainEvent;

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
    public function normalize(DomainEvent $event)
    {
        $event = $this->resetAsyncState($event);

        return json_encode($event->toArray(), $this->options);
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias(): string
    {
        return 'json';
    }
}
