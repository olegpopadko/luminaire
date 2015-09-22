<?php

namespace AppBundle\Tests\Functional\Controller\Admin;

use AppBundle\Tests\Functional\TestCase;

class UserControllerUpdateTest extends TestCase
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
            ['/admin/user/1/edit'],
        ];
    }

    public function testUserUpdate()
    {
        $container = static::$kernel->getContainer();

        $em = $container->get('doctrine')->getManager();

        $userId = $em->getRepository('AppBundle:User')->findOneBy(['email' => 'operator@test.com'])->getId();

        $crawler = $this->client->request('GET', "/admin/user/{$userId}/edit");

        $email    = 'new_second@test.com';
        $username = 'new_second';
        $fullName = 'New Second Test User';
        $password = 'new_second';
        $timezone = 'Asia/Almaty';

        $form = $crawler->selectButton('Update')->form([
            'appbundle_user[user][email]'             => $email,
            'appbundle_user[user][username]'          => $username,
            'appbundle_user[user][fullName]'          => $fullName,
            'appbundle_user[plainPassword][password]' => $password,
            'appbundle_user[plainPassword][confirm]'  => $password,
            'appbundle_user[user][timezone]'          => $timezone,
        ]);
        $form['appbundle_user[user][roles]'][0]->tick();
        $form['appbundle_user[user][roles]'][1]->untick();

        $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirect());
        $this->assertEquals("/admin/user/{$userId}/edit", $this->client->getResponse()->headers->get('Location'));

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

        $this->successLoginCheck('new_second', 'new_second');
    }

    /**
     * @depends testUserUpdate
     */
    public function testUserUpdateWithoutPassword()
    {
        $crawler = $this->client->request('GET', "/admin/user/{$this->getCreatedUser()->getId()}/edit");

        $timezone = 'Arctic/Longyearbyen';

        $form = $crawler->selectButton('Update')->form([
            'appbundle_user[user][timezone]' => $timezone,
        ]);

        $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirect());
        $this->assertEquals(
            "/admin/user/{$this->getCreatedUser()->getId()}/edit",
            $this->client->getResponse()->headers->get('Location')
        );

        $this->successLoginCheck('new_second', 'new_second');
    }

    /**
     * @depends testUserUpdate
     */
    public function testUserUpdateEmailIsAlreadyUsed()
    {
        $crawler = $this->client->request('GET', "/admin/user/{$this->getCreatedUser()->getId()}/edit");

        $form = $crawler->selectButton('Update')->form([
            'appbundle_user[user][email]' => 'admin@test.com',
        ]);

        $crawler = $this->client->submit($form);

        $this->assertEquals(
            'This email is already used.',
            $crawler->filter('small.error')->first()->text()
        );
    }

    /**
     * @depends testUserUpdate
     */
    public function testUserUpdateUserNameIsAlreadyUsed()
    {
        $crawler = $this->client->request('GET', "/admin/user/{$this->getCreatedUser()->getId()}/edit");

        $form = $crawler->selectButton('Update')->form([
            'appbundle_user[user][email]'    => 'new_second2@test.com',
            'appbundle_user[user][username]' => 'admin',
        ]);

        $crawler = $this->client->submit($form);

        $this->assertEquals(
            'This username is already used.',
            $crawler->filter('small.error')->first()->text()
        );
    }

    /**
     * @depends testUserUpdate
     */
    public function testUserUpdateWrongEmail()
    {
        $crawler = $this->client->request('GET', "/admin/user/{$this->getCreatedUser()->getId()}/edit");

        $form = $crawler->selectButton('Update')->form([
            'appbundle_user[user][email]' => 'wrong_email',
        ]);

        $crawler = $this->client->submit($form);

        $this->assertEquals(
            'This value is not a valid email address.',
            $crawler->filter('small.error')->first()->text()
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     * @depends testUserUpdate
     */
    public function testUserUpdateEmptyTimezone()
    {
        $crawler = $this->client->request('GET', "/admin/user/{$this->getCreatedUser()->getId()}/edit");

        $form = $crawler->selectButton('Update')->form([
            'appbundle_user[user][timezone]' => '',
        ]);

        $this->client->submit($form);
    }

    /**
     * @depends testUserUpdate
     */
    public function testUserUpdateErrorField()
    {
        $crawler = $this->client->request('GET', "/admin/user/{$this->getCreatedUser()->getId()}/edit");

        $form = $crawler->selectButton('Update')->form([
            'appbundle_user[user][email]'             => '',
            'appbundle_user[user][username]'          => '',
            'appbundle_user[user][fullName]'          => '',
            'appbundle_user[plainPassword][password]' => '',
            'appbundle_user[plainPassword][confirm]'  => '',
            'appbundle_user[user][timezone]'          => 'Asia/Almaty',
        ]);
        $form['appbundle_user[user][roles]'][0]->untick();

        $crawler = $this->client->submit($form);

        $count = 0;
        foreach ($crawler->filter('small.error') as $error) {
            if ($count === $crawler->filter('small.error')->count() - 1) {
                break;
            }
            $this->assertEquals('This value should not be blank.', $error->nodeValue);
            $count++;
        }
        $this->assertEquals(3, $count);
        $this->assertEquals(
            'This collection should contain exactly 1 element.',
            $crawler->filter('small.error')->last()->text()
        );
    }

    private function getCreatedUser()
    {
        $container = static::$kernel->getContainer();

        $em = $container->get('doctrine')->getManager();

        return $em->getRepository('AppBundle:User')->findOneBy(['email' => 'new_second@test.com']);
    }
}
