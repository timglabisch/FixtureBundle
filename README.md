# Tg\FixtureBundle

Highly customizable and fast loader for fixtures with support for testing.

## Createing Fixtures
Fixtures are defined in YAML Twig Files.
In each bundle you can define these Yaml Twig files in [Bundle]/Fixture/Resouces/[...].yml.twig

### YAML File

for example if you want to create fixtures for a `Blog-Post`, you could create such a file:


AppBundle/Fixture/Resouces/blogpost.yml.twig

```yml
{# faker \Faker\Generator #}

blogpost:
    id: foo
    author: {{ faker.name }}
    
comments:
    # ...
```

You can use the full power of twig, for example to create loops etc.
You even get autocompletion for libs like Faker.

### Loading the YAML File

for every root key (`blogpost` and `comments` in this example) 
you've to define a fixture loader and tag it with `fixture_yaml`.

example fixture loader for `blogpost`

```php
<?php

namespace AppBundle\Fixture\Loader;

use Tg\Bundle\FixtureBundle\Event\LoadFixtureFileEvent;
use Tg\Bundle\FixtureBundle\YamlFixtureInterface;

class BlogPostYamlLoader implements YamlFixtureInterface
{
    public function supports(LoadFixtureFileEvent $event)
    {
        return isset($event->getContent()['blogpost']);
    }

    public function load(LoadFixtureFileEvent $event)
    {
        // do whatever you want here.
        // you can create a doctrine entity, index the blogpost to elasticsearch or whatever you want.
    }

}
```

now everytime you run `bin/console fixtures:load --f` the load method is executed for every
blogpost fixture.

### Preparing your Database

most time you have to prepare your datastorage before loading fixtures.
here is a small example which supports doctrine.

AppBundle/Fixture/Subscriber/CleanupDatabaseSubscriber.php and tag it with `kernel.event_subscriber`.

```php
<?php

namespace AppBundle\Fixture\Subscriber;


use Doctrine\ORM\EntityManager;
use Tg\Bundle\FixtureBundle\Cleaner\DbalCleaner;
use Tg\Bundle\FixtureBundle\Event\PrepareLoadFixturesEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CleanupDatabaseSubscriber implements EventSubscriberInterface
{

    /** @var EntityManager */
    private $em;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public static function getSubscribedEvents()
    {
        return [
            PrepareLoadFixturesEvent::class => [['onPrepareLoadFixturesEvent']]
        ];
    }

    public function onPrepareLoadFixturesEvent(PrepareLoadFixturesEvent $event)
    {
        (new DbalCleaner($this->em->getConnection()))->cleanup($event->getContext());
    }

}
```

you can easily extend this to support any kind of data storage.

## Loading Fixtures in Tests

loading fixtures in tests can be very helpful.
This bundle comes with a helper trait for Doctrine, but you can write your own if you use different 
Datastorages.

You need to extend from the KernelTestCase (WebTestCase is also fine) 
if you want to load Fixtures.

Example:

```
<?php

namespace Tests\AppBundle\Controller;

use Tg\Bundle\FixtureBundle\Integration\PHPUnit\PHPUnitDoctrineHelper;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DefaultControllerTest extends KernelTestCase
{
    use PHPUnitDoctrineHelper;

    public function setUp()
    {
        parent::setUp();
        $this->ensureDoctrineSchemaIsReady();
    }

    public function testIndex()
    {
        $this->assertTrue(true);
    }
}

```

This code is highly optimized, running testIndex 100 times, takes around 800ms in my dev system.
Keep in mind that the database gets truncated for every run and the schema is up to date :)
