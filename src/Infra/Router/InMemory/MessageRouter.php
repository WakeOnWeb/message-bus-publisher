<?php

namespace WakeOnWeb\MessageBusPublisher\Infra\Router\InMemory;

use WakeOnWeb\MessageBusPublisher\Domain\Message\DefaultMessageIdentifierResolver;
use WakeOnWeb\MessageBusPublisher\Domain\Message\MessageIdentifierResolverInterface;
use WakeOnWeb\MessageBusPublisher\Domain\Router\MessageRouterInterface;

/**
 * MessageRouter.
 *
 * @uses \MessageRouterInterface
 *
 * @author Stephane PY <s.py@wakeonweb.com>
 */
class MessageRouter implements MessageRouterInterface
{
    /** var array */
    private $routes = [];

    /** var MessageIdentifierResolverInterface */
    private $messageIdentifierResolver;

    /**
     * @param MessageIdentifierResolverInterface $messageIdentifierResolver messageIdentifierResolver
     */
    public function __construct(MessageIdentifierResolverInterface $messageIdentifierResolver = null)
    {
        $this->messageIdentifierResolver = $messageIdentifierResolver ?: new DefaultMessageIdentifierResolver();
    }

    /**
     * @param string $message  message
     * @param string $target target
     */
    public function addRoute(string $message, string $target): void
    {
        $this->routes[$message][] = $target;
    }

    /**
     * {@inheritdoc}
     */
    public function route($message): array
    {
        $messageId = get_class($message);

        if (false === array_key_exists($messageId, $this->routes)) {
            return [];
        }

        return $this->routes[$messageId];
    }
}
