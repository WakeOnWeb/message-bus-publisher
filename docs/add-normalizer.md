# Add a normalizer

## Add a service definition

```yaml
<service class="FQCN\To\Normalizer" public="false">
    <tag name="wow.message_bus_publisher.normalizer" />
</service>
```

## Create your normalizer class

```php
<?php

namespace FQCN\To;

use WakeOnWeb\EventBusPublisher\Domain\Normalizer\NormalizerInterface;
use WakeOnWeb\EventBusPublisher\Infra\Normalizer\AbstractNormalizer;
use Prooph\Common\Messaging\DomainEvent;

class Normalizer extends AbstractNormalizer implements NormalizerInterface
{
    /**
     * @{inheritdoc}
     */
    public function normalize(DomainEvent $event)
    {
        //return ...
    }

    /**
     * @{inheritdoc}
     */
    public function getAlias(): string
    {
        return 'my_new_normalizer';
    }
}

```

Why inherit from AbstractNormalizer ?
Because messages from prooph have a metadata to inform buses than message is already handled async or not, and we must not give this information to the target.


Then, you have to define `my_new_normalizer` to the target.

[Back to home](../README.md)
