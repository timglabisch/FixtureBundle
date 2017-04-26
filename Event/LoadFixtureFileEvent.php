<?php

namespace Tg\Bundle\FixtureBundle\Event;

use Tg\Bundle\FixtureBundle\FixtureContext;
use Symfony\Component\EventDispatcher\Event;

class LoadFixtureFileEvent extends Event
{
    /** @var FixtureContext */
    private $context;

    /** @var string */
    private $filename;

    /** @var string */
    private $content;

    /**
     * @param FixtureContext $context
     * @param string $filename
     * @param string $content
     */
    public function __construct(FixtureContext $context, $filename, $content)
    {
        $this->context = $context;
        $this->filename = $filename;
        $this->content = $content;
    }

    /**
     * @return FixtureContext
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

}