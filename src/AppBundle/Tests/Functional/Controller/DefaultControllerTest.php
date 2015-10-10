<?php

namespace AppBundle\Tests\Functional\Controller;

class DefaultControllerTest extends \AppBundle\Tests\Functional\TestCase
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
    }
}
