<?php

namespace Tg\Bundle\FixtureBundle;


class FixtureConfigurationService
{
    /**
     * @var FixtureConfiguration[]
     */
    private $configurations = [];

    /**
     * @param FixtureConfiguration $configurations
     */
    public function __construct(array $configurations)
    {
        $this->configurations = $configurations;
    }

    /**
     * @return FixtureConfiguration[]
     */
    public function getConfigurations()
    {
        return $this->configurations;
    }

}