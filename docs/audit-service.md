# Audit service

```
wakeonweb_event_bus_publisher:
    audit:
        drivers:
            services:
                - acme.my_audit_driver.service
```

Then add a service with the name `acme.my_audit_driver.service` and implements the interface `WakeOnWeb\EventBusPublisher\Domain\Audit\AuditorInterface:`

```php
<?php

namespace Acme;

use Prooph\Common\Messaging\DomainEvent;
use WakeOnWeb\EventBusPublisher\Domain\Gateway\GatewayResponse;

class MyAuditDriver implements AuditorInterface
{
    public function registerListenedEvent(DomainEvent $event, bool $routed)
    {
        // ...
    }

    public function registerTargetedEvent(DomainEvent $event, string $targetId, GatewayResponse $gatewayResponse)
    {
        // ...
    }
}
```

[Back to home](../README.md)
