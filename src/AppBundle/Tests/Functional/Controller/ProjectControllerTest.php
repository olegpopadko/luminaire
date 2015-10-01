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
    public function testPageIsSuccessful($url)
    {
        $this->logInAdmin();
        $this->client->request('GET', $url);

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * @return array
     */
    public function urlProvider()
    {
        return [
            ['/project/'],
            ['/project/TP'],
            ['/project/TP/edit'],
            ['/project/new'],
        ];
    }

    /**
     * @dataProvider urlProviderOperatorRestricted
     */
    public function testPageIsOperatorRestricted($url)
    {
        $this->logInOperator();
        $this->client->request('GET', $url);

        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return array
     */
    public function urlProviderOperatorRestricted()
    {
        return [
            ['/project/TP/edit'],
            ['/project/TPM'],
            ['/project/TPA'],
            ['/project/TPM/edit'],
            ['/project/TPA/edit'],
            ['/project/new'],
        ];
    }

    /**
     * @dataProvider urlProviderOperatorAllowed
     */
    public function testPageIsOperatorAllowed($url)
    {
        $this->logInAdmin();
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
    public function testPageIsManagerRestricted($url)
    {
        $this->logInOperator();
        $this->client->request('GET', $url);

        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return array
     */
    public function urlProviderManagerRestricted()
    {
        return [
            ['/project/TP/edit'],
            ['/project/TPA'],
            ['/project/TPA/edit'],
        ];
    }

    /**
     * @dataProvider urlProviderManagerAllowed
     */
    public function testPageIsManagerAllowed($url)
    {
        $this->logInAdmin();
        $this->client->request('GET', $url);

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * @return array
     */
    public function urlProviderManagerAllowed()
    {
        return [
            ['/project/TPM'],
            ['/project/TPM/edit'],
            ['/project/new'],
        ];
    }
}
