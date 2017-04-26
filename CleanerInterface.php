<?php

namespace Tg\Bundle\FixtureBundle;


interface CleanerInterface
{
    public function cleanup(FixtureContext $context);
}