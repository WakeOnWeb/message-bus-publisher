<?php

namespace WakeOnWeb\MessageBusPublisher\Domain\Normalizer;

use Prooph\Common\Messaging\DomainMessage;

/**
 * NormalizerInterface.
 *
 * @author Stephane PY <s.py@wakeonweb.com>
 */
interface NormalizerInterface
{
    /**
     *  Normalizer DomainMessage
     *  We can't ensure returned data is a string
     *  It'll depend on targets.
     */
    public function normalize(DomainMessage $message);

    /**
     *  Which alias to be configured.
     */
    public function getAlias(): string;
}
