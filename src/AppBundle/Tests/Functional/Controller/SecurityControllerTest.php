<?php

namespace AppBundle\Tests\Functional\Controller;

class SecurityControllerTest extends \AppBundle\Tests\Functional\TestCase
{
    public function testLoginRedirect()
    {
        $this->logInOperator();
        $this->client->request('GET', '/login');
        $this->assertTrue($this->client->getResponse()->isRedirect('/'));
    }

    public function testRedirectForAnonymous()
    {
        $client = static::createClient();

        $client->request('GET', '/');

        $this->assertTrue($client->getResponse()->isRedirect('http://localhost/login'));
    }

    public function testLogin()
    {
        $this->successLoginCheck('admin', 'admin');
    }

    public function testLoginViaEmail()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');

        $form = $crawler->filter('button')->form([
            '_username' => 'admin@test.com',
            '_password' => 'admin',
        ]);

        $client->submit($form);

        $this->assertTrue($client->getResponse()->isRedirect('http://localhost/'));
    }

    public function testLoginInvalidCredentials()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');

        $form = $crawler->filter('button')->form([
            '_username' => 'failed',
            '_password' => 'failed',
        ]);

        $client->submit($form);

        $crawler = $client->followRedirect();

        $this->assertEquals('Invalid credentials.', $crawler->filter('div.login > div')->text());
    }

    public function testLoginRegistrationButton()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');

        $link = $crawler->filter('a[href="/sign_up"]')->link();

        $crawler = $client->click($link);

        $this->assertEquals('Sign up', $crawler->filter('title')->text());
    }
}
