<?php

namespace WakeOnWeb\EventBusPublisher\Infra\Queue;

use Bernard\Message\PlainMessage;
use WakeOnWeb\EventBusPublisher\Domain\Target\TargetRepositoryInterface;

class BernardReceiver
{
    const MESSAGE_NAME = 'DomainEvent';

    private $targetRepository;

    public function __construct(TargetRepositoryInterface $targetRepository)
    {
        $this->targetRepository = $targetRepository;
    }

    /**
     * @param PlainMessage $message
     */
    public function __invoke(PlainMessage $message)
    {
        $target = $this->targetRepository->findRequired($message->get('target'));
        $domainEvent = unserialize($message->get('domain_event'));

        $normalizedData = $target->getNormalizer()->normalize($domainEvent);

        $target->getGateway()->send($normalizedData);
    }

}
