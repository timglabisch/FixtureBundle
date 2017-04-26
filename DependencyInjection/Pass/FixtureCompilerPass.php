<?php

namespace Tg\Bundle\FixtureBundle\DependencyInjection\Pass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class FixtureCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        foreach ($container->findTaggedServiceIds('fixture') as $id => $tags) {
            $container
                ->findDefinition('fixture_bundle.subscriber.fixture')
                ->addMethodCall('addLoader', array(new Reference($id)));
        }

       foreach ($container->findTaggedServiceIds('fixture_yaml') as $id => $tags) {
            $container
                ->findDefinition('fixture_bundle.subscriber.yaml_fixture')
                ->addMethodCall('addLoader', array(new Reference($id)));
        }
    }
}