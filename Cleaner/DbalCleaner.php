<?php

namespace Tg\Bundle\FixtureBundle\Cleaner;

use Doctrine\DBAL\Connection;
use Tg\Bundle\FixtureBundle\CleanerInterface;
use Tg\Bundle\FixtureBundle\FixtureContext;

class DbalCleaner implements CleanerInterface
{
    private $connection = null;

    private static $runs = 0;

    /**
     * @var string[]
     */
    private static $tables = [];

    public function __construct(
        Connection $connection
    ) {
        $this->connection = $connection;
    }


    private function getTables(FixtureContext $context, $tableRegex = '.*') {

        $cacheKey = serialize($context).';'.$tableRegex;

        if (isset(static::$tables[$cacheKey])) {
            return static::$tables[$cacheKey];
        }

        $tables = $this->connection->query('SHOW TABLES;')->fetchAll();

        $buffer = [];

        foreach ($tables as $table) {

            if (!preg_match('/'.$tableRegex.'/i', current($table))) {
                continue;
            }

            $buffer[] = current($table);
        }

        return static::$tables[$cacheKey] = $buffer;
    }

    private function getTablesQuery(FixtureContext $fixtureContext, $query)
    {
        return implode(';', array_map(function($table) use ($query) { return $query.' '.$table; }, $this->getTables($fixtureContext)));
    }

    public function cleanup(FixtureContext $context)
    {

        if (!$this->getTables($context)) {
            return;
        }

        if (static::$runs++ === 0) {
            $this->connection->executeQuery($this->getTablesQuery($context,'drop table'));
        } else {
            $this->connection->executeQuery($this->getTablesQuery($context,'truncate'));
        }
    }

}