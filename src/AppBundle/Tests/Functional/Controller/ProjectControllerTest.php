<?php

namespace AppBundle\Tests\Functional\Controller;

use AppBundle\Tests\Functional\TestCase;

/**
 * Class ProjectControllerTest
 */
class ProjectControllerTest extends TestCase
{
    /**
     * @dataProvider urlProvider
     */
    public function testPageSuccessful($method, $url)
    {
        $this->logInAdmin();
        $this->client->request($method, $url);

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * @return array
     */
    public function urlProvider()
    {
        return [
            ['GET', '/project/'],
            ['GET', '/project/TP'],
            ['POST', '/project/'],
            ['PUT', '/project/TP'],
            ['GET', '/project/TP/edit'],
            ['GET', '/project/new'],
            ['GET', '/project/TPM'],
            ['PUT', '/project/TPM'],
            ['GET', '/project/TPM/edit'],
            ['GET', '/project/TPA'],
            ['PUT', '/project/TPA'],
            ['GET', '/project/TPA/edit'],
        ];
    }

    /**
     * @dataProvider urlProviderOperatorRestricted
     */
    public function testPageOperatorRestricted($method, $url)
    {
        $this->logInOperator();
        $this->client->request($method, $url);

        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return array
     */
    public function urlProviderOperatorRestricted()
    {
        return [
            ['POST', '/project/'],
            ['PUT', '/project/TP'],
            ['GET', '/project/TP/edit'],
            ['GET', '/project/new'],
            ['GET', '/project/TPM'],
            ['PUT', '/project/TPM'],
            ['GET', '/project/TPM/edit'],
            ['GET', '/project/TPA'],
            ['PUT', '/project/TPA'],
            ['GET', '/project/TPA/edit'],
        ];
    }

    /**
     * @dataProvider urlProviderOperatorAllowed
     */
    public function testPageOperatorAllowed($url)
    {
        $this->logInOperator();
        $this->client->request('GET', $url);

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * @return array
     */
    public function urlProviderOperatorAllowed()
    {
        return [
            ['/project/'],
            ['/project/TP'],
        ];
    }

    /**
     * @dataProvider urlProviderManagerRestricted
     */
    public function testPageManagerRestricted($method, $url)
    {
        $this->logInManager();
        $this->client->request($method, $url);

        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return array
     */
    public function urlProviderManagerRestricted()
    {
        return [
            ['PUT', '/project/TP'],
            ['GET', '/project/TP/edit'],
            ['GET', '/project/TPA'],
            ['PUT', '/project/TPA'],
            ['GET', '/project/TPA/edit'],
        ];
    }

    /**
     * @dataProvider urlProviderManagerAllowed
     */
    public function testPageManagerAllowed($method, $url)
    {
        $this->logInManager();
        $this->client->request($method, $url);

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * @return array
     */
    public function urlProviderManagerAllowed()
    {
        return [
            ['GET', '/project/'],
            ['POST', '/project/'],
            ['GET', '/project/new'],
            ['GET', '/project/TPM'],
            ['PUT', '/project/TPM'],
            ['GET', '/project/TPM/edit'],
        ];
    }
}
