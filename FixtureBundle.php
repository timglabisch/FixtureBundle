<?php

namespace Tg\Bundle\FixtureBundle;

use Tg\Bundle\FixtureBundle\DependencyInjection\Pass\FixtureCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class FixtureBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new FixtureCompilerPass());
    }

}
