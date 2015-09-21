<?php

namespace AppBundle\Tests\Functional;

use AppBundle\Tests\Functional\Controller\DataFixtures\LoadRoleData;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Loader;
use AppBundle\Tests\Functional\Controller\DataFixtures\LoadUserData;

class TestCase extends WebTestCase
{
    public static function setUpBeforeClass()
    {
        static::bootKernel();
        $container = static::$kernel->getContainer();
        $loader = new Loader();
        $loader->addFixture(new LoadRoleData());
        $fixture = (new LoadUserData());
        $fixture->setContainer($container);
        $loader->addFixture($fixture);
        $purger   = new ORMPurger();
        /** @var \Doctrine\ORM\EntityManagerInterface $em */
        $em = $container->get('doctrine')->getManager();
        $executor = new ORMExecutor($em, $purger);
        $executor->execute($loader->getFixtures());
    }
}
