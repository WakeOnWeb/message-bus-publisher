Driver Symfony Configuration
============================

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
                    normalizer: json
                z:
                    amqp:
                        connection: xxx # PhpAmqpLib\Connection\AMQPStreamConnection
                        queue: xxx
                    normalizer: array
            routing:
                x:
                    - App\Event\UserCreatedEvent
                y:
                    - App\Event\UserCreatedEvent
```


[Back to home](../README.md)
