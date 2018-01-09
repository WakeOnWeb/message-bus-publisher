# WakeOnWeb EventBusPublisher

## Installation

`composer.json`

```
    "require": [
        "wakeonweb/event-bus-publisher": "^0.1"
    ],
```

If you use **Symfony**, you can load the bundle `WakeOnWeb\EventBusPublisher\App\Bundle\WakeonwebEventBusPublisherBundle`.

## Usage

### Synchronous

```
wakeonweb_event_bus_publisher:
    publishing:
        listened_prooph_buses: [sync_external_outgoing_event_bus]
        delivery_mode: synchronous
    driver:
        # ... see driver chapter
```

Define the prooph buses this publisher will listen to.
Once an event is dispatched in theses buses, it'll dispatch events to targets.

### Asynchronous

```
wakeonweb_event_bus_publisher:
    publishing:
        listened_prooph_buses: [sync_external_outgoing_event_bus]
        delivery_mode: asynchronous
        queue_name: my_queue_name.{target}
    driver:
        # ... see driver chapter
```

Define the prooph buses this publisher will listen to.
Once an event is dispatched in theses buses, it'll guess route then dispatch this event
in a dedicated queue called `my_queue_name.{target}` where {target} is the target name.

Then consume theses messages to dispatch them to targets:

```
./bin/console bernard:consume my_queue_name.target_x
```

## Audit

This library can audit listened events and targeted events:

```yaml
wakeonweb_event_bus_publisher:
    audit:
        drivers:
            monolog:
                level: notice
                only_routed_events: true # do you want to log each events ?
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
