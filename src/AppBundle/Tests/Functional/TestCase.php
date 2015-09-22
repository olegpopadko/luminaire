<?php

namespace AppBundle\Tests\Functional;

use AppBundle\Tests\Functional\Controller\DataFixtures\LoadRoleData;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Loader;
use AppBundle\Tests\Functional\Controller\DataFixtures\LoadUserData;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class TestCase extends WebTestCase
{
    /**
     * @var Client
     */
    protected $client;

    public function setUp()
    {
        $this->client = static::createClient();
    }

    public static function setUpBeforeClass()
    {
        static::bootKernel();
        $container = static::$kernel->getContainer();
        $loader    = new Loader();
        $loader->addFixture(new LoadRoleData());
        $fixture = (new LoadUserData());
        $fixture->setContainer($container);
        $loader->addFixture($fixture);
        $purger = new ORMPurger();
        /** @var \Doctrine\ORM\EntityManagerInterface $em */
        $em       = $container->get('doctrine')->getManager();
        $executor = new ORMExecutor($em, $purger);
        $executor->execute($loader->getFixtures());
    }

    private function logIn($username, $roles = ['ROLE_OPERATOR'])
    {
        $session = $this->client->getContainer()->get('session');

        $em = $this->client->getContainer()->get('doctrine')->getManager();
        $user = $em->getRepository('AppBundle:User')->findOneBy(['username' => $username]);

        $firewall = 'main';
        $token    = new UsernamePasswordToken($user, null, $firewall, $roles);
        $session->set('_security_' . $firewall, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }

    protected function logInAdmin()
    {
        $this->logIn('admin', ['ROLE_ADMIN']);
    }

    protected function logInOperator()
    {
        $this->logIn('operator');
    }

    protected function logInManager()
    {
        $this->logIn('manager', ['ROLE_MANAGER']);
    }
}
