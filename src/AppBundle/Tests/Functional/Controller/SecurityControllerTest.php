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

        $client->request('GET', '/');

        $this->assertTrue($client->getResponse()->isRedirect('http://localhost/login'));

        $crawler = $client->followRedirect();

        $form = $crawler->filter('button')->form([
            '_username' => 'admin',
            '_password' => 'secret',
        ]);

        $client->submit($form);

        $this->assertTrue($client->getResponse()->isRedirect('http://localhost/'));
    }

    public function testLoginFailed()
    {
        $client = static::createClient();

        $client->request('GET', '/');

        $this->assertTrue($client->getResponse()->isRedirect('http://localhost/login'));

        $crawler = $client->followRedirect();

        $form = $crawler->filter('button')->form([
            '_username' => 'failed',
            '_password' => 'failed',
        ]);

        $client->submit($form);

        $crawler = $client->followRedirect();

        $this->assertEquals(
            'Invalid credentials.',
            $crawler->filter('div.login > div')->extract(['_text'])[0]
        );
    }
}
