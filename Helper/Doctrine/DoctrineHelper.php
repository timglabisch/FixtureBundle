<?php

namespace Tg\Bundle\FixtureBundle\Helper\Doctrine;


use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;

class DoctrineHelper
{
    /** @var EntityManager */
    private $defaultEm;

    private $schemaFine = false;
    /**
     * @param EntityManager $defaultEm
     */
    public function __construct(EntityManager $defaultEm)
    {
        $this->defaultEm = $defaultEm;
    }

    private function getEntityManager(EntityManager $em = null)
    {
        if ($em) {
            return $em;
        }

        return $this->defaultEm;
    }

    public function ensureSchema(EntityManager $em = null)
    {
        if ($this->schemaFine) {
            return;
        }

        $em = $this->getEntityManager($em);
        (new SchemaTool($em))
            ->updateSchema($em->getMetadataFactory()->getAllMetadata())
        ;

        $this->schemaFine = true;
    }

}