# Audit doctrine-orm

```yaml
wakeonweb_message_bus_publisher:
    audit:
        drivers:
            doctrine_orm: ~

## OR 

wakeonweb_message_bus_publisher:
    audit:
        drivers:
            doctrine_orm:
                only_routed_messages: false
                entity_manager: default
                listened_message_entity: WakeOnWeb\MessageBusPublisher\Infra\Audit\DoctrineORM\Entity\ListenedMessage
                targeted_message_entity: WakeOnWeb\MessageBusPublisher\Infra\Audit\DoctrineORM\Entity\TargetedMessageWithResponse
```

Define the doctrine mapping:

```
doctrine:
    orm:
        auto_generate_proxy_classes: '%kernel.debug%'
        naming_strategy: doctrine.orm.naming_strategy.underscore
        auto_mapping: true
        mappings:
            message_bus_publisher_audit:
                is_bundle: false
                type: xml
                dir: '%kernel.project_dir%/vendor/wakeonweb/message-bus-publisher/src/Infra/Audit/DoctrineORM/Resources/entity'
                prefix: 'WakeOnWeb\MessageBusPublisher\Infra\Audit\DoctrineORM\Entity'
                alias: App
```

[Back to home](../README.md)
