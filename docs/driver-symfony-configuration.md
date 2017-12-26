Driver Symfony Configuration
===============

```
wakeonweb_event_bus_publisher:
    driver:
        in_memory:
            targets:
                x:
                    service:
                        id: App\Acme\GatewayX
                y:
                    http:
                        endpoint: https://.....
                    normalizer: App\Acme\NormalizerX
                z:
                    amqp:
                        connection: xxx # PhpAmqpLib\Connection\AMQPStreamConnection
                        queue: xxx
                    normalizer: App\Acme\NormalizerY
            routing:
                x:
                    - App\Event\UserCreatedEvent
                y:
                    - App\Event\UserCreatedEvent
```

