<?php

namespace AppBundle\Tests\Functional\Controller\Admin;

use AppBundle\Tests\Functional\TestCase;

class UserControllerTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->logInAdmin();
    }

    /**
     * @dataProvider uriRestrictionsProvider
     */
    public function testRestrictions($uri)
    {
        $this->logInOperator();
        $this->client->request('GET', $uri);
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());

        $this->logInManager();
        $this->client->request('GET', $uri);
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function uriRestrictionsProvider()
    {
        return [
            ['/admin/user/'],
            ['/admin/user/new'],
        ];
    }

    public function testUserList()
    {
        $crawler = $this->client->request('GET', '/admin/user/');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->click($crawler->selectLink('Create new user')->link());
        $this->assertEquals('User creation', $crawler->filter('title')->text());

        $crawler = $this->client->request('GET', '/admin/user/');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->click($crawler->selectLink('Edit')->link());
        $this->assertEquals('User edit', $crawler->filter('title')->text());
    }

    public function testUserCreate()
    {
        $crawler = $this->client->request('GET', '/admin/user/new');

        $container = static::$kernel->getContainer();

        $em = $container->get('doctrine')->getManager();

        $email    = 'new@test.com';
        $username = 'new';
        $fullName = 'New Test User';
        $password = 'new';
        $timezone = 'Asia/Almaty';

        $form = $crawler->selectButton('Create')->form([
            'appbundle_user[email]'    => $email,
            'appbundle_user[username]' => $username,
            'appbundle_user[fullName]' => $fullName,
            'appbundle_user[password]' => $password,
            'appbundle_user[timezone]' => $timezone,
            'appbundle_user[roles][1]' => $em->getRepository('AppBundle:Role')->findManagerRole()->getId(),
        ]);

        $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirect());
        $this->assertEquals(
            1,
            preg_match('/^\/admin\/user\/[0-9]+\/edit/', $this->client->getResponse()->headers->get('Location'))
        );

        $users = $em->getRepository('AppBundle:User')->findBy([
            'email' => $email,
        ]);

        $this->assertCount(1, $users);
        $user = $users[0];
        $this->assertEquals($email, $user->getEmail());
        $this->assertEquals($username, $user->getUsername());
        $this->assertEquals($fullName, $user->getFullName());
        $this->assertEquals($timezone, $user->getTimezone());
        $this->assertEquals([$em->getRepository('AppBundle:Role')->findManagerRole()], $user->getRoles());
        $this->assertTrue($container->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY'));

        $this->successLoginCheck('new', 'new');
    }

    /**
     * @depends testUserCreate
     */
    public function testUserCreateEmailIsAlreadyUsed()
    {
        $crawler = $this->client->request('GET', '/admin/user/new');

        $form = $crawler->selectButton('Create')->form([
            'appbundle_user[email]' => 'new@test.com',
        ]);

        $crawler = $this->client->submit($form);

        $this->assertEquals(
            'This email is already used.',
            $crawler->filter('small.error')->first()->text()
        );
    }

    /**
     * @depends testUserCreate
     */
    public function testUserCreateUserNameIsAlreadyUsed()
    {
        $crawler = $this->client->request('GET', '/admin/user/new');

        $form = $crawler->selectButton('Create')->form([
            'appbundle_user[email]'    => 'new2@test.com',
            'appbundle_user[username]' => 'new',
        ]);

        $crawler = $this->client->submit($form);

        $this->assertEquals(
            'This username is already used.',
            $crawler->filter('small.error')->first()->text()
        );
    }

    public function testUserCreateWrongEmail()
    {
        $crawler = $this->client->request('GET', '/admin/user/new');

        $form = $crawler->selectButton('Create')->form([
            'appbundle_user[email]' => 'wrong_email',
        ]);

        $crawler = $this->client->submit($form);

        $this->assertEquals(
            'This value is not a valid email address.',
            $crawler->filter('small.error')->first()->text()
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testUserCreateEmptyTimezone()
    {
        $crawler = $this->client->request('GET', '/admin/user/new');

        $form = $crawler->selectButton('Create')->form([
            'appbundle_user[timezone]' => '',
        ]);

        $this->client->submit($form);
    }

    public function testUserCreateErrorField()
    {
        $crawler = $this->client->request('GET', '/admin/user/new');

        $form = $crawler->selectButton('Create')->form([
            'appbundle_user[timezone]' => 'Asia/Almaty',
        ]);

        $crawler = $this->client->submit($form);

        $count = 0;
        foreach ($crawler->filter('small.error') as $error) {
            if ($count === $crawler->filter('small.error')->count() - 1) {
                break;
            }
            $this->assertEquals('This value should not be blank.', $error->nodeValue);
            $count++;
        }
        $this->assertEquals(4, $count);
        $this->assertEquals(
            'This collection should contain exactly 1 element.',
            $crawler->filter('small.error')->last()->text()
        );
    }
}
