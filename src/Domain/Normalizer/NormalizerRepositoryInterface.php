<?php

namespace WakeOnWeb\MessageBusPublisher\Domain\Normalizer;

/**
 * NormalizerRepositoryInterface.
 *
 * @author Stephane PY <s.py@wakeonweb.com>
 */
interface NormalizerRepositoryInterface
{
    public function find(string $normalizer): ?NormalizerInterface;

    public function addNormalizer(NormalizerInterface $normalizer);
}
