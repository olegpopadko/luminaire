<?php

namespace AppBundle\Tests\Functional\Controller;

class SecurityControllerTest extends \AppBundle\Tests\Functional\TestCase
{
    public function testRedirectForAnonymous()
    {
        $client = static::createClient();

        $client->request('GET', '/');

        $this->assertTrue($client->getResponse()->isRedirect('http://localhost/login'));
    }

    public function testLogin()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');

        $form = $crawler->filter('button')->form([
            '_username' => 'admin',
            '_password' => 'secret',
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

        $this->assertEquals('Invalid credentials.', $crawler->filter('div.login > div')->html());
    }

    public function testLoginRegistrationButton()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');

        $link = $crawler->filter('a[href="/sign_up"]')->link();

        $crawler = $client->click($link);

        $this->assertEquals('Sign up', $crawler->filter('title')->html());
    }
}
