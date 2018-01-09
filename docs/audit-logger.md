# Audit logger

```
wakeonweb_event_bus_publisher:
    audit:
        drivers:
            monolog:
                level: notice
                only_routed_events: true # do you want to log each events ?
```

You can easily log events in a dedicated handler:

```
monolog:
    handlers:
        audit:
            type: stream
            path: "%kernel.logs_dir%/audit.%kernel.environment%.log"
            level: debug
            channels: ["wow.event_bus_publisher.audit"]
```

[Back to home](../README.md)
