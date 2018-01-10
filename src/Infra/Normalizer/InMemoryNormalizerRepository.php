<?php

namespace WakeOnWeb\EventBusPublisher\Infra\Normalizer;

use WakeOnWeb\EventBusPublisher\Domain\Normalizer\NormalizerRepositoryInterface;
use WakeOnWeb\EventBusPublisher\Domain\Normalizer\NormalizerInterface;
use WakeOnWeb\EventBusPublisher\Domain\Exception\NormalizerNotFoundException;

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
