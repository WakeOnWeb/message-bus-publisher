<?php

namespace WakeOnWeb\MessageBusPublisher\Infra\Normalizer;

use WakeOnWeb\MessageBusPublisher\Domain\Normalizer\NormalizerRepositoryInterface;
use WakeOnWeb\MessageBusPublisher\Domain\Normalizer\NormalizerInterface;
use WakeOnWeb\MessageBusPublisher\Domain\Exception\NormalizerNotFoundException;

class InMemoryNormalizerRepository implements NormalizerRepositoryInterface
{
    /** @var NormalizerInterface[] : */
    private $normalizers = [];

    /**
     * {@inheritdoc}
     */
    public function find(string $normalizer): NormalizerInterface
    {
        if (false === array_key_exists($normalizer, $this->normalizers)) {
            throw NormalizerNotFoundException::createFromId($normalizer);
        }

        return $this->normalizers[$normalizer];
    }

    /**
     * {@inheritdoc}
     */
    public function addNormalizer(NormalizerInterface $normalizer)
    {
        $this->normalizers[$normalizer->getAlias()] = $normalizer;
    }
}
