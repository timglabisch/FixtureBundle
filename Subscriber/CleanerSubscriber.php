<?php

namespace Tg\Bundle\FixtureBundle\Subscriber;


use Tg\Bundle\FixtureBundle\CleanerInterface;
use Tg\Bundle\FixtureBundle\Event\PrepareLoadFixturesEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CleanerSubscriber implements EventSubscriberInterface
{

    /** @var CleanerInterface */
    private $cleaner;

    /**
     * @param CleanerInterface $cleaner
     */
    public function __construct(CleanerInterface $cleaner)
    {
        $this->cleaner = $cleaner;
    }

    public static function getSubscribedEvents()
    {
        return [
            PrepareLoadFixturesEvent::class => [['onPrepareLoadFixturesEvent']]
        ];
    }

    public function onPrepareLoadFixturesEvent(PrepareLoadFixturesEvent $event)
    {
        $this->cleaner->cleanup($event->getContext());
    }

}