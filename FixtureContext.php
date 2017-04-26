<?php

namespace Tg\Bundle\FixtureBundle;


use Symfony\Component\Console\Output\OutputInterface;

class FixtureContext
{
    /**
     * @var OutputInterface
     */
    private $output;

    private $env;


    /**
     * @param $output
     * @param $env
     * @param $type
     * @param array $tables
     */
    public function __construct(OutputInterface $output, $env)
    {
        $this->output = $output;
        $this->env = $env;
    }

    /**
     * @return OutputInterface
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * @return mixed
     */
    public function getEnv()
    {
        return $this->env;
    }

}