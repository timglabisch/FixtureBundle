<?php

namespace Tg\Bundle\FixtureBundle\Event;

use Tg\Bundle\FixtureBundle\FixtureContext;
use Symfony\Component\EventDispatcher\Event;

class PrepareLoadFixturesEvent extends Event
{
    /** @var FixtureContext */
    private $context;

    /**
     * @param FixtureContext $context
     */
    public function __construct(FixtureContext $context)
    {
        $this->context = $context;
    }

    /**
     * @return FixtureContext
     */
    public function getContext()
    {
        return $this->context;
    }

}