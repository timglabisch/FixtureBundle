<?php

namespace Tg\Bundle\FixtureBundle\Cleaner;

use Tg\Bundle\FixtureBundle\CleanerInterface;
use Tg\Bundle\FixtureBundle\FixtureContext;

class SqlCleaner implements CleanerInterface
{
    /** @var string */
    private $uri;

    /** @var DbalCleaner */
    private $dbalCleaner;

    /**
     * @var string[]
     */
    private $tables = [];

    public function __construct(
        $uri
    ) {
        $this->uri = $uri;
    }

    private function getDbalCleaner()
    {
        if (!$this->dbalCleaner) {
            $this->dbalCleaner = new DbalCleaner(\Doctrine\DBAL\DriverManager::getConnection(
                ['url' => $this->uri],
                new \Doctrine\DBAL\Configuration()
            ));
        }

        return $this->dbalCleaner;

    }

    public function cleanup(FixtureContext $context)
    {
        return $this->getDbalCleaner()->cleanup($context);
    }

}