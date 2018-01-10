<?php

namespace WakeOnWeb\MessageBusPublisher\App\Bundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class NormalizersPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $definition = $container->findDefinition('wow.message_bus_publisher.normalizer_repository');

        $taggedServices = $container->findTaggedServiceIds('wow.message_bus_publisher.normalizer');

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addNormalizer', array(new Reference($id)));
        }
    }
}
