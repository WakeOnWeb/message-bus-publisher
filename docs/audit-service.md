# Audit service

```
wakeonweb_message_bus_publisher:
    audit:
        drivers:
            services:
                - acme.my_audit_driver.service
```

Then add a service with the name `acme.my_audit_driver.service` and implements the interface `WakeOnWeb\MessageBusPublisher\Domain\Audit\AuditorInterface:`

```php
<?php

namespace Acme;

use Prooph\Common\Messaging\DomainMessage;
use WakeOnWeb\MessageBusPublisher\Domain\Gateway\GatewayResponse;

class MyAuditDriver implements AuditorInterface
{
    public function registerListenedMessage(DomainMessage $message, bool $routed)
    {
        // ...
    }

    public function registerTargetedMessage(DomainMessage $message, string $targetId, GatewayResponse $gatewayResponse)
    {
        // ...
    }
}
```

[Back to home](../README.md)
