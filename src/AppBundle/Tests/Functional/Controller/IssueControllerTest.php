<?php

namespace AppBundle\Tests\Functional\Controller;

use AppBundle\Tests\Functional\TestCase;

/**
 * Class IssueControllerTest
 */
class IssueControllerTest extends TestCase
{
    /**
     * @dataProvider urlProvider
     */
    public function testIssuePageSuccessful($method, $url)
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
            ['GET', '/project/TP/issues'],
            ['GET', '/project/TP/issue/new'],
            ['POST', '/project/TP/issue'],
            ['GET', '/issue/TP-1'],
            ['GET', '/issue/TP-2'],
            ['PUT', '/issue/TP-1'],
            ['PUT', '/issue/TP-2'],
            ['GET', '/issue/TP-1/edit'],
            ['GET', '/issue/TP-2/edit'],
            ['GET', '/project/TPM/issues'],
            ['GET', '/project/TPM/issue/new'],
            ['POST', '/project/TPM/issue'],
            ['GET', '/issue/TPM-1'],
            ['GET', '/issue/TPM-2'],
            ['PUT', '/issue/TPM-1'],
            ['PUT', '/issue/TPM-2'],
            ['GET', '/issue/TPM-1/edit'],
            ['GET', '/issue/TPM-2/edit'],
            ['GET', '/project/TPA/issues'],
            ['GET', '/project/TPA/issue/new'],
            ['POST', '/project/TPA/issue'],
            ['GET', '/issue/TPA-1'],
            ['GET', '/issue/TPA-2'],
            ['PUT', '/issue/TPA-1'],
            ['PUT', '/issue/TPA-2'],
            ['GET', '/issue/TPA-1/edit'],
            ['GET', '/issue/TPA-2/edit'],
        ];
    }

    /**
     * @dataProvider urlProviderOperatorRestricted
     */
    public function testIssuePageOperatorRestricted($method, $url)
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
            ['GET', '/project/TPM/issues'],
            ['GET', '/project/TPM/issue/new'],
            ['POST', '/project/TPM/issue'],
            ['GET', '/issue/TPM-1'],
            ['GET', '/issue/TPM-2'],
            ['PUT', '/issue/TPM-1'],
            ['PUT', '/issue/TPM-2'],
            ['GET', '/issue/TPM-1/edit'],
            ['GET', '/issue/TPM-2/edit'],
            ['GET', '/project/TPA/issues'],
            ['GET', '/project/TPA/issue/new'],
            ['POST', '/project/TPA/issue'],
            ['GET', '/issue/TPA-1'],
            ['GET', '/issue/TPA-2'],
            ['PUT', '/issue/TPA-1'],
            ['PUT', '/issue/TPA-2'],
            ['GET', '/issue/TPA-1/edit'],
            ['GET', '/issue/TPA-2/edit'],
        ];
    }

    /**
     * @dataProvider urlProviderOperatorAllowed
     */
    public function testIssuePageOperatorAllowed($method, $url)
    {
        $this->logInOperator();
        $this->client->request($method, $url);

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * @return array
     */
    public function urlProviderOperatorAllowed()
    {
        return [
            ['GET', '/project/TP/issues'],
            ['GET', '/project/TP/issue/new'],
            ['POST', '/project/TP/issue'],
            ['GET', '/issue/TP-1'],
            ['GET', '/issue/TP-2'],
            ['PUT', '/issue/TP-1'],
            ['PUT', '/issue/TP-2'],
            ['GET', '/issue/TP-1/edit'],
            ['GET', '/issue/TP-2/edit'],
        ];
    }

    /**
     * @dataProvider urlProviderManagerRestricted
     */
    public function testIssuePageManagerRestricted($method, $url)
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
            ['GET', '/project/TP/issues'],
            ['GET', '/project/TP/issue/new'],
            ['POST', '/project/TP/issue'],
            ['GET', '/issue/TP-1'],
            ['GET', '/issue/TP-2'],
            ['PUT', '/issue/TP-1'],
            ['PUT', '/issue/TP-2'],
            ['GET', '/issue/TP-1/edit'],
            ['GET', '/issue/TP-2/edit'],
            ['GET', '/project/TPA/issues'],
            ['GET', '/project/TPA/issue/new'],
            ['POST', '/project/TPA/issue'],
            ['GET', '/issue/TPA-1'],
            ['GET', '/issue/TPA-2'],
            ['PUT', '/issue/TPA-1'],
            ['PUT', '/issue/TPA-2'],
            ['GET', '/issue/TPA-1/edit'],
            ['GET', '/issue/TPA-2/edit'],
        ];
    }

    /**
     * @dataProvider urlProviderManagerAllowed
     */
    public function testIssuePageManagerAllowed($method, $url)
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
            ['GET', '/project/TPM/issues'],
            ['GET', '/project/TPM/issue/new'],
            ['POST', '/project/TPM/issue'],
            ['GET', '/issue/TPM-1'],
            ['GET', '/issue/TPM-2'],
            ['PUT', '/issue/TPM-1'],
            ['PUT', '/issue/TPM-2'],
            ['GET', '/issue/TPM-1/edit'],
            ['GET', '/issue/TPM-2/edit'],
        ];
    }
}
