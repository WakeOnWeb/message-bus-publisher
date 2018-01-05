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
     * @param \IteratorAggregate $normalizers normalizers
     */
    public function __construct(\IteratorAggregate $normalizers)
    {
        foreach ($normalizers as $normalizer) {
            $this->addNormalizer($normalizer);
        }
    }

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

    private function addNormalizer(NormalizerInterface $normalizer)
    {
        $this->normalizers[$normalizer->getAlias()] = $normalizer;
    }
}
