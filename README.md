# WakeOnWeb MessageBusPublisher

**important, we don't ensure BC compatibility in 0.x tags, we'll ensure it from 1.x only**

## Installation

`composer.json`

```
    "require": [
        "wakeonweb/message-bus-publisher": "^0.1"
    ],
```

If you use **Symfony**, you can load the bundle `WakeOnWeb\MessageBusPublisher\App\Bundle\WakeonwebMessageBusPublisherBundle`.

## Usage

### Synchronous

```
wakeonweb_message_bus_publisher:
    publishing:
        listened_prooph_buses: [event_bus]
        delivery_mode: synchronous
    driver:
        # ... see driver chapter
```

Define the prooph buses this publisher will listen to.
Once an message is dispatched in theses buses, it'll dispatch messages to targets.

### Asynchronous

```
wakeonweb_message_bus_publisher:
    publishing:
        listened_prooph_buses: [event_bus]
        delivery_mode: asynchronous
        queue_name: my_queue_name.{target}
    driver:
        # ... see driver chapter
```

Define the prooph buses this publisher will listen to.
Once an message is dispatched in theses buses, it'll guess route then dispatch this message
in a dedicated queue called `my_queue_name.{target}` where {target} is the target name.

Then consume theses messages to dispatch them to targets:

```
./bin/console bernard:consume my_queue_name.target_x
```

## Audit

This library can audit listened messages and targeted messages:

```yaml
wakeonweb_message_bus_publisher:
    audit:
        drivers:
            monolog:
                level: notice
                only_routed_messages: true # do you want to log each messages ?
            doctrine_orm: ~
            services:
                - x
                - y
```

You can have as many drivers as you want.

To go further in configuration, look at documentation below.

- [Monolog](docs/audit-logger.md)
- [DoctrineORM](docs/audit-doctrine-orm.md)
- [Service](docs/audit-service.md)

## Flow

![flow](docs/flow.png)

## Drivers implemented

- [Symfony configuration](docs/driver-symfony-configuration.md)
- [DoctrineORM](docs/driver-doctrine-orm.md)

## Other documentation

- [How to add a normalizer](docs/add-normalizer.md)
