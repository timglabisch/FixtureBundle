<?php

namespace Tg\Bundle\FixtureBundle\Integration\PHPUnit;


use Doctrine\ORM\EntityManager;
use Tg\Bundle\FixtureBundle\Cleaner\DbalCleaner;
use Tg\Bundle\FixtureBundle\FixtureContext;
use Tg\Bundle\FixtureBundle\Helper\Doctrine\DoctrineHelper;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpKernel\KernelInterface;

trait PHPUnitDoctrineHelper
{
    /** @var DoctrineHelper */
    private static $phpunitDoctrineHelper;

    /** @var KernelInterface */
    private static $fixtureKernel;

    private function ensureFixtureKernel()
    {
        if (!static::$fixtureKernel) {
            /** @var $kernel KernelInterface */
            static::$fixtureKernel = static::createKernel(['env' => 'test']);
            static::$fixtureKernel->boot();
        }
    }

    private function getPHPUnitDoctrineHelper()
    {
        if (!static::$phpunitDoctrineHelper) {
            static::$phpunitDoctrineHelper = static::$fixtureKernel->getContainer()->get('fixture_bundle.doctrine_helper');
        }

        return static::$phpunitDoctrineHelper;
    }

    public function ensureDoctrineSchemaIsReady(EntityManager $em = null)
    {
        static::ensureFixtureKernel();

        $em = $em ?: static::$fixtureKernel->getContainer()->get('doctrine.orm.entity_manager');

        (new DbalCleaner($em->getConnection()))->cleanup(new FixtureContext(new NullOutput(), 'test'));

        $this->getPHPUnitDoctrineHelper()->ensureSchema($em);
    }
}