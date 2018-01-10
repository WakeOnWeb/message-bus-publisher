<?php

namespace WakeOnWeb\MessageBusPublisher\App\Bundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use WakeOnWeb\MessageBusPublisher\App\Bundle\DependencyInjection\Compiler\NormalizersPass;

/**
 * WakeonwebMessageBusPublisher.
 *
 * @uses \Bundle
 *
 * @author Stephane PY <s.py@wakeonweb.com>
 */
class WakeonwebMessageBusPublisherBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new NormalizersPass());
    }
}
