<?php

namespace App\Infrastructure\Application\QueryBus\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class QueryBusPass implements CompilerPassInterface
{
    private const QUERYBUS_HANDLER = 'queryBus';

    public function process(ContainerBuilder $container)
    {
        if (!$container->has(self::QUERYBUS_HANDLER)) {
            return;
        }

        $definition = $container->findDefinition(self::QUERYBUS_HANDLER);
        $services = $container->findTaggedServiceIds('travel.queryHandler');

        foreach ($services as $service => $attributes) {
            if (isset($attributes[0]['command'])) {
                $queryName = $attributes[0]['command'];
                $definition->addMethodCall('addHandler', [$queryName, new Reference($service)]);
            }
        }
    }
}
