<?php

namespace Tg\Bundle\FixtureBundle\Subscriber;

use Tg\Bundle\FixtureBundle\Event\LoadFixtureFileEvent;
use Tg\Bundle\FixtureBundle\YamlFixtureInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class YamlFixtureSubscriber implements EventSubscriberInterface
{

    /** @var YamlFixtureInterface[] */
    private $yamlLoader = [];

    /**
     * @param array $yamlLoader
     */
    public function __construct(array $yamlLoader = [])
    {
        $this->yamlLoader = $yamlLoader;
    }

    public function addLoader(YamlFixtureInterface $yamlFixture)
    {
        $this->yamlLoader[] = $yamlFixture;
    }

    public static function getSubscribedEvents()
    {
        return [
            LoadFixtureFileEvent::class => [['onLoadFixtureFileEvent']]
        ];
    }

    public function onLoadFixtureFileEvent(LoadFixtureFileEvent $event)
    {
        $found = false;
        foreach ($this->yamlLoader as $loader) {
            if (!$loader->supports($event)) {
                continue;
            }


            $event->getContext()->getOutput()->writeln(sprintf(
                '<info>%s</info> imported by <info>%s</info>',
                $event->getFilename(),
                get_class($loader)
            ));

            $loader->load($event);
            $found = true;
        }

        if (!$found) {
            $event->getContext()->getOutput()->writeln(sprintf(
                '<error>Could not find Fixture Loader for File %s</error>',
                $event->getFilename()
            ));
        }
    }

}