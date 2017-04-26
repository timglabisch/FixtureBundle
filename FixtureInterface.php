<?php

namespace Tg\Bundle\FixtureBundle;


interface FixtureInterface
{
    public function supports(FixtureContext $context);

    public function prepare(FixtureContext $context);
    public function load(FixtureContext $context);
    public function finish(FixtureContext $context);
}