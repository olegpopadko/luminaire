<?php

namespace AppBundle\Tests\Functional\Controller;

use Symfony\Component\DomCrawler\Crawler;

class RegistrationControllerTest extends \AppBundle\Tests\Functional\TestCase
{
    public function testRegistration()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/sign_up');

        $email    = 'new@test.com';
        $username = 'new';
        $fullName = 'New Test User';
        $password = 'new';
        $timezone = 'Asia/Almaty';

        $form = $crawler->filter('button')->form([
            'appbundle_registration[email]'              => $email,
            'appbundle_registration[username]'           => $username,
            'appbundle_registration[fullName]'           => $fullName,
            'appbundle_registration[password][password]' => $password,
            'appbundle_registration[password][confirm]'  => $password,
            'appbundle_registration[timezone]'           => $timezone,
        ]);

        $client->submit($form);

        $this->assertTrue($client->getResponse()->isRedirect('/'));

        $container = static::$kernel->getContainer();

        $em = $container->get('doctrine')->getManager();

        $users = $em->getRepository('AppBundle:User')->findBy([
            'email' => $email,
        ]);

        $this->assertCount(1, $users);
        $user = $users[0];
        $this->assertEquals($email, $user->getEmail());
        $this->assertEquals($username, $user->getUsername());
        $this->assertEquals($fullName, $user->getFullName());
        $this->assertEquals($timezone, $user->getTimezone());
        $this->assertEquals([$em->getRepository('AppBundle:Role')->findOperatorRole()], $user->getRoles());
        $this->assertTrue($container->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testRegistrationEmptyTimezone()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/sign_up');

        $form = $crawler->filter('button')->form([
            'appbundle_registration[timezone]' => '',
        ]);

        $client->submit($form);
    }

    public function testRegistrationWrongEmail()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/sign_up');

        $form = $crawler->filter('button')->form([
            'appbundle_registration[email]' => 'wrong_email',
        ]);

        $crawler = $client->submit($form);

        $this->assertEquals(
            'This value is not a valid email address.',
            $crawler->filter('small.error')->first()->html()
        );
    }

    public function testRegistrationErrorField()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/sign_up');

        $form = $crawler->filter('button')->form([
            'appbundle_registration[email]'              => '',
            'appbundle_registration[username]'           => '',
            'appbundle_registration[fullName]'           => '',
            'appbundle_registration[password][password]' => '',
            'appbundle_registration[password][confirm]'  => '',
            'appbundle_registration[timezone]'           => 'Asia/Almaty',
        ]);

        $crawler = $client->submit($form);

        $crawler->filter('small.error')->each(function ($error) {
            /** @var Crawler $error */
            $this->assertEquals('This value should not be blank.', $error->html());
        });
    }
}
