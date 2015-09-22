<?php

namespace AppBundle\Tests\Functional\Controller;

use AppBundle\Tests\Functional\TestCase;

class ProfileControllerTest extends TestCase
{
    public function testProfileRestrictions()
    {
        //Operator can view self profile
        $operator = $this->logInOperator();
        $crawler  = $this->client->request('GET', '/profile/' . $operator->getId());
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        //Operator can edit only self profile
        $crawler = $this->client->click($crawler->selectLink('Edit')->link());
        $this->assertEquals('Profile edit', $crawler->filter('title')->text());

        //Manager can view self profile
        $manager = $this->logInManager();
        $crawler = $this->client->request('GET', '/profile/' . $manager->getId());
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        //Manager can edit only self profile
        $crawler = $this->client->click($crawler->selectLink('Edit')->link());
        $this->assertEquals('Profile edit', $crawler->filter('title')->text());

        //Manager can view all profiles
        $crawler = $this->client->request('GET', '/profile/' . $operator->getId());
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        //Manager cannot edit other profiles
        $this->assertCount(0, $crawler->selectLink('Edit'));
        $this->client->request('GET', '/profile/' . $operator->getId() . '/edit');
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());

        //Operator can view all profiles
        $operator = $this->logInOperator();
        $crawler  = $this->client->request('GET', '/profile/' . $manager->getId());
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        //Operator cannot edit other profiles
        $this->assertCount(0, $crawler->selectLink('Edit'));
        $this->client->request('GET', '/profile/' . $manager->getId() . '/edit');
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());

        //Admin can view self profile
        $admin   = $this->logInAdmin();
        $crawler = $this->client->request('GET', '/profile/' . $admin->getId());
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        //Admin can edit self profile
        $crawler = $this->client->click($crawler->selectLink('Edit')->link());
        $this->assertEquals('Profile edit', $crawler->filter('title')->text());

        //Admin can view and edit all profiles
        $crawler = $this->client->request('GET', '/profile/' . $manager->getId());
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->click($crawler->selectLink('Edit')->link());
        $this->assertEquals('Profile edit', $crawler->filter('title')->text());

        //Admin can view and edit all profiles
        $crawler = $this->client->request('GET', '/profile/' . $operator->getId());
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->click($crawler->selectLink('Edit')->link());
        $this->assertEquals('Profile edit', $crawler->filter('title')->text());

        //Operator can view all profiles
        $this->logInOperator();
        $crawler = $this->client->request('GET', '/profile/' . $admin->getId());
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        //Operator cannot edit other profiles
        $this->assertCount(0, $crawler->selectLink('Edit'));
        $this->client->request('GET', '/profile/' . $admin->getId() . '/edit');
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());

        //Manager can view all profiles
        $this->logInManager();
        $crawler = $this->client->request('GET', '/profile/' . $admin->getId());
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        //Manager cannot edit other profiles
        $this->assertCount(0, $crawler->selectLink('Edit'));
        $this->client->request('GET', '/profile/' . $admin->getId() . '/edit');
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testProfileEdit()
    {
        $operator = $this->logInOperator();
        $crawler  = $this->client->request('GET', '/profile/' . $operator->getId() . '/edit');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $email    = 'new_second@test.com';
        $username = 'new_second';
        $fullName = 'New Second Test User';
        $password = 'new_second';
        $timezone = 'Asia/Almaty';

        $form = $crawler->selectButton('Update')->form([
            'appbundle_profile[email]'              => $email,
            'appbundle_profile[username]'           => $username,
            'appbundle_profile[fullName]'           => $fullName,
            'appbundle_profile[password][password]' => $password,
            'appbundle_profile[password][confirm]'  => $password,
            'appbundle_profile[timezone]'           => $timezone,
        ]);
        $this->assertFalse(isset($form['appbundle_profile[roles]']));

        $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirect());
        $this->assertEquals(
            1,
            preg_match('/^\/profile\/[0-9]+/', $this->client->getResponse()->headers->get('Location'))
        );

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
    }
}
