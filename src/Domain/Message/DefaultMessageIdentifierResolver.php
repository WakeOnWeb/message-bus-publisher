<?php

namespace WakeOnWeb\MessageBusPublisher\Domain\Message;

class DefaultMessageIdentifierResolver implements MessageIdentifierResolverInterface
{
    /**
     * {@inheritdoc}
     */
    public function resolve($message): string
    {
        if (is_object($message)) {

            if (method_exists($message, 'messageName')) {
                return $message->messageName();
            }

            return get_class($message);
        }

        if (false === is_scalar($message)) {
            throw new \LogicException(sprintf('%s can resolve class or scalar messages.', __CLASS__));
        }

        return $message;
    }
}
