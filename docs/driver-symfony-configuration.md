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
                        queue: xxx
                        message_name: EventBusExternalMessage
                    normalizer: array
            routing:
                x:
                    - App\Event\UserCreatedEvent
                y:
                    - App\Event\UserCreatedEvent
```


[Back to home](../README.md)
