<?php

namespace Tg\Bundle\FixtureBundle;


class FixtureConfiguration
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var string | null
     */
    private $dsn;

    /**
     * @param string $type
     * @param null|string $dsn
     */
    public function __construct($type, $dsn)
    {
        $this->type = $type;
        $this->dsn = $dsn;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return null|string
     */
    public function getDsn()
    {
        return $this->dsn;
    }

}