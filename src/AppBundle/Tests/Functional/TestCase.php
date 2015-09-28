<?php

namespace AppBundle\Tests\Functional;

use AppBundle\Tests\Functional\Controller\DataFixtures\LoadIssueData;
use AppBundle\Tests\Functional\Controller\DataFixtures\LoadIssuePriorityData;
use AppBundle\Tests\Functional\Controller\DataFixtures\LoadIssueResolutionData;
use AppBundle\Tests\Functional\Controller\DataFixtures\LoadIssueStatusData;
use AppBundle\Tests\Functional\Controller\DataFixtures\LoadIssueTypeData;
use AppBundle\Tests\Functional\Controller\DataFixtures\LoadProjectData;
use AppBundle\Tests\Functional\Controller\DataFixtures\LoadRoleData;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Loader;
use AppBundle\Tests\Functional\Controller\DataFixtures\LoadUserData;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * Class TestCase
 * @SuppressWarnings(PHPMD)
 */
class TestCase extends WebTestCase
{
    /**
     * @var Client
     */
    protected $client;

    /**
     *
     */
    public function setUp()
    {
        $this->client = static::createClient();
    }

    /**
     *
     */
    public static function setUpBeforeClass()
    {
        static::bootKernel();
        $loader = new Loader();
        foreach (self::getFixtures() as $fixture) {
            $loader->addFixture($fixture);
        }
        $purger = new ORMPurger();
        /** @var \Doctrine\ORM\EntityManagerInterface $em */
        $container = static::$kernel->getContainer();
        $em        = $container->get('doctrine')->getManager();
        $executor  = new ORMExecutor($em, $purger);
        $executor->execute($loader->getFixtures());
    }

    /**
     * @return array
     */
    private static function getFixtures()
    {
        $userFixture = (new LoadUserData());
        $userFixture->setContainer(static::$kernel->getContainer());
        return [
            new LoadRoleData(),
            $userFixture,
            new LoadProjectData(),
            new LoadIssuePriorityData(),
            new LoadIssueResolutionData(),
            new LoadIssueStatusData(),
            new LoadIssueTypeData(),
            new LoadIssueData(),
        ];
    }

    /**
     * @param $username
     * @param array $roles
     * @return \AppBundle\Entity\User
     */
    protected function logIn($username, $roles = ['ROLE_OPERATOR'])
    {
        $session = $this->client->getContainer()->get('session');

        $em   = $this->client->getContainer()->get('doctrine')->getManager();
        $user = $em->getRepository('AppBundle:User')->findOneBy(['username' => $username]);

        $firewall = 'main';
        $token    = new UsernamePasswordToken($user, null, $firewall, $roles);
        $session->set('_security_' . $firewall, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
        return $user;
    }

    /**
     * @return \AppBundle\Entity\User
     */
    protected function logInAdmin()
    {
        return $this->logIn('admin', ['ROLE_ADMIN']);
    }

    /**
     * @return \AppBundle\Entity\User
     */
    protected function logInOperator()
    {
        return $this->logIn('operator');
    }

    /**
     * @return \AppBundle\Entity\User
     */
    protected function logInManager()
    {
        return $this->logIn('manager', ['ROLE_MANAGER']);
    }

    /**
     * @param $id
     * @return object
     */
    protected function get($id)
    {
        return static::$kernel->getContainer()->get($id);
    }
}
