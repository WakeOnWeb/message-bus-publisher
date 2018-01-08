<?php

namespace WakeOnWeb\EventBusPublisher\App\Bundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use WakeOnWeb\EventBusPublisher\App\Bundle\DependencyInjection\Compiler\NormalizersPass;

/**
 * WakeonwebEventBusPublisher.
 *
 * @uses \Bundle
 *
 * @author Stephane PY <s.py@wakeonweb.com>
 */
class WakeonwebEventBusPublisherBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new NormalizersPass());
    }
}
