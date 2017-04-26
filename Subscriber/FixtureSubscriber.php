<?php

namespace Tg\Bundle\FixtureBundle\Subscriber;


use Tg\Bundle\FixtureBundle\Event\LoadFixturesEvent;
use Tg\Bundle\FixtureBundle\Event\PostLoadFixturesEvent;
use Tg\Bundle\FixtureBundle\Event\PrepareLoadFixturesEvent;
use Tg\Bundle\FixtureBundle\FixtureContext;
use Tg\Bundle\FixtureBundle\FixtureInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class FixtureSubscriber implements EventSubscriberInterface
{
    /** @var FixtureInterface[] */
    private $loader = [];

    /**
     * FixtureSubscriber constructor.
     * @param FixtureInterface[] $loader
     */
    public function __construct(array $loader = [])
    {
        $this->loader = $loader;
    }

    public function addLoader(FixtureInterface $loader)
    {
        $this->loader[] = $loader;
    }

    public static function getSubscribedEvents()
    {
        return [
            LoadFixturesEvent::class => [['onLoadFixtureEvent']],
            PostLoadFixturesEvent::class => [['onPostLoadFixturesEvent']],
            PrepareLoadFixturesEvent::class => [['onPrepareLoadFixturesEvent']]
        ];
    }

    /**
     * @param FixtureContext $context
     * @return FixtureInterface[]
     */
    private function getSuppertedLoaders(FixtureContext $context)
    {
        return array_values(array_filter(array_map(function(FixtureInterface $loader) use ($context) {
            return $loader->load($context);
        }, $this->loader)));
    }

    public function onPrepareLoadFixturesEvent(PrepareLoadFixturesEvent $event)
    {
        foreach ($this->getSuppertedLoaders($event->getContext()) as $loader) {
            $event->getContext()->getOutput()->writeln(sprintf('[Pre] loader <info>%s</info>', get_class($loader)));
            $loader->load($event->getContext());
        }
    }

    public function onLoadFixtureEvent(LoadFixturesEvent $event)
    {
        foreach ($this->getSuppertedLoaders($event->getContext()) as $loader) {
            $event->getContext()->getOutput()->writeln(sprintf('[Load] loader <info>%s</info>', get_class($loader)));
            $loader->load($event->getContext());
        }
    }

    public function onPostLoadFixturesEvent(PostLoadFixturesEvent $event)
    {
        foreach ($this->getSuppertedLoaders($event->getContext()) as $loader) {
            $event->getContext()->getOutput()->writeln(sprintf('[Post] loader <info>%s</info>', get_class($loader)));
            $loader->finish($event->getContext());
        }
    }
}