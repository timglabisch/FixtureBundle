<?php

namespace Tg\Bundle\FixtureBundle;


use Tg\Bundle\FixtureBundle\Event\LoadFixtureFileEvent;

interface YamlFixtureInterface
{
    public function supports(LoadFixtureFileEvent $event);

    public function load(LoadFixtureFileEvent $event);
}