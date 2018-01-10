# Audit doctrine-orm

```yaml
wakeonweb_event_bus_publisher:
    audit:
        drivers:
            doctrine_orm: ~

## OR 

wakeonweb_event_bus_publisher:
    audit:
        drivers:
            doctrine_orm:
                only_routed_events: false
                entity_manager: default
                listened_event_entity: WakeOnWeb\EventBusPublisher\Infra\Audit\DoctrineORM\Entity\ListenedEvent
                targeted_event_entity: WakeOnWeb\EventBusPublisher\Infra\Audit\DoctrineORM\Entity\TargetedEventWithResponse
```

Define the doctrine mapping:

```
doctrine:
    orm:
        auto_generate_proxy_classes: '%kernel.debug%'
        naming_strategy: doctrine.orm.naming_strategy.underscore
        auto_mapping: true
        mappings:
            event_bus_publisher_audit:
                is_bundle: false
                type: xml
                dir: '%kernel.project_dir%/vendor/wakeonweb/event-bus-publisher/src/Infra/Audit/DoctrineORM/Resources/entity'
                prefix: 'WakeOnWeb\EventBusPublisher\Infra\Audit\DoctrineORM\Entity'
                alias: App
```

[Back to home](../README.md)
