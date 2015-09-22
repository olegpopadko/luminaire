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
        $operator = $this->logIn('operator1');
        $crawler  = $this->client->request('GET', '/profile/' . $operator->getId() . '/edit');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $email    = 'new_second@test.com';
        $username = 'new_second';
        $fullName = 'New Second Test User';
        $password = 'new_second';
        $timezone = 'Asia/Almaty';

        $form = $crawler->selectButton('Update')->form([
            'appbundle_profile[user][email]'             => $email,
            'appbundle_profile[user][username]'          => $username,
            'appbundle_profile[user][fullName]'          => $fullName,
            'appbundle_profile[plainPassword][password]' => $password,
            'appbundle_profile[plainPassword][confirm]'  => $password,
            'appbundle_profile[user][timezone]'          => $timezone,
        ]);
        $this->assertFalse(isset($form['appbundle_profile[user][roles]']));

        $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirect('/profile/' . $operator->getId()));

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

        $this->successLoginCheck('new_second', 'new_second');
    }

    public function testProfileEditWithoutPassword()
    {
        $manager = $this->logInManager();
        $crawler  = $this->client->request('GET', '/profile/' . $manager->getId() . '/edit');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $fullName = 'New Second Full Name';

        $form = $crawler->selectButton('Update')->form([
            'appbundle_profile[user][fullName]' => $fullName,
        ]);

        $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirect('/profile/' . $manager->getId()));

        $this->successLoginCheck('manager', 'manager');
    }

    public function testAssignedIssues()
    {
        $operator = $this->logInOperator();
        /** @var \Doctrine\Common\Persistence\ObjectManager $em */
        $em = $this->get('doctrine')->getManager();

        /** @var \AppBundle\Entity\Project $project */
        $project = $em->getRepository('AppBundle:Project')->findOneByCode('TP');
        $project->addUser($operator);
        $em->persist($project);
        $em->flush();

        $crawler  = $this->client->request('GET', '/profile/' . $operator->getId());

        $this->assertEquals('TP-1 Test issue summary', $crawler->filter('.assigned_issues a')->first()->text());

        $this->logInManager();

        $crawler  = $this->client->request('GET', '/profile/' . $operator->getId());

        $this->assertEquals('There is no available issues', $crawler->filter('.assigned_issues td')->first()->text());
    }
}
