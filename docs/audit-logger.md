# Audit logger

```
wakeonweb_message_bus_publisher:
    audit:
        drivers:
            monolog:
                level: notice
                only_routed_messages: true # do you want to log each messages ?
```

You can easily log messages in a dedicated handler:

```
monolog:
    handlers:
        audit:
            type: stream
            path: "%kernel.logs_dir%/audit.%kernel.environment%.log"
            level: debug
            channels: ["wow.message_bus_publisher.audit"]
```

[Back to home](../README.md)
