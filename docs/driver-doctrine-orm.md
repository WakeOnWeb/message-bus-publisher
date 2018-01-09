# Add a normalizer


## Default configuration

```yaml
wakeonweb_event_bus_publisher:
    driver:
        doctrine_orm: ~

# then

doctrine:
    orm:
        mappings:
            event_bus_publisher:
                is_bundle: false
                type: xml
                dir: '%kernel.project_dir%/vendor/wakeonweb/event-bus-publisher/src/Infra/Driver/DoctrineORM/Resources/entity'
                prefix: 'WakeOnWeb\EventBusPublisher\Infra\Driver\DoctrineORM\Entity'
                alias: App

wakeonweb_event_bus_publisher:
    driver:
        doctrine_orm:
            entity_manager: default
            target_entity: WakeOnWeb\EventBusPublisher\Infra\Driver\DoctrineORM\Entity\Target
            route_entity: WakeOnWeb\EventBusPublisher\Infra\Driver\DoctrineORM\Entity\Route

# OR 
```

## Override  ...

```yaml
wakeonweb_event_bus_publisher:
    driver:
        doctrine_orm:
            entity_manager: your_own
            target_entity: Your\Own\Entity\Target
            route_entity: Your\Own\Entity\Route
```

*Be careful, Your target & route entities should inherit from provided entities.*

## Create your configuration

```php
<?php

$target = new Target('my_target_name', new AmqpGatewayDefinition('target_queue'), 'array');
$route = new Route('\my\event\class', $target);

$em->persist($target);
$em->persist($route);
$em->flush();
```


[Back to home](../README.md)
