<?php

namespace Tg\Bundle\FixtureBundle\Cleaner;


use Tg\Bundle\FixtureBundle\CleanerInterface;
use Tg\Bundle\FixtureBundle\FixtureContext;

class ChainCleaner implements CleanerInterface
{

    /** @var CleanerInterface[] */
    private $cleaners = [];

    public function addCleaner(CleanerInterface $cleaner)
    {
        $this->cleaners[] = $cleaner;
    }

    public function cleanup(FixtureContext $context)
    {
        foreach ($this->cleaners as $cleaner) {
            $cleaner->cleanup($context);
        }
    }
}