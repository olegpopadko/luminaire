<?php

namespace AppBundle\Tests\Functional\Controller\Admin;

use AppBundle\Entity\User;
use AppBundle\Tests\Functional\TestCase;

class ProjectControllerTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->logInManager();
    }

    public function testProjectCreate()
    {
        $crawler = $this->client->request('GET', '/project/new');

        $container = static::$kernel->getContainer();

        $em = $container->get('doctrine')->getManager();

        $label    = 'New Project';
        $summary  = 'New Project Summury';
        $users    = $em->getRepository('AppBundle:User')->findAll();
        $userIds = array_map(function (User $user) {
            return $user->getId();
        }, $users);

        $form = $crawler->selectButton('Create')->form([
            'appbundle_project[label]'   => $label,
            'appbundle_project[summary]' => $summary,
            'appbundle_project[users]'   => $userIds,
        ]);

        $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirect());
        $this->assertEquals('/project/NP', $this->client->getResponse()->headers->get('Location'));

        $projects = $em->getRepository('AppBundle:Project')->findByCode('NP');

        $this->assertCount(1, $projects);
        $project = $projects[0];
        $this->assertEquals($label, $project->getLabel());
        $this->assertEquals($summary, $project->getSummary());
        $this->assertEquals($userIds, array_map(function (User $user) {
            return $user->getId();
        }, $project->getUsers()->toArray()));
    }

    public function testProjectCreateLabelIsAlreadyUsed()
    {
        $crawler = $this->client->request('GET', '/project/new');

        $form = $crawler->selectButton('Create')->form([
            'appbundle_project[label]' => 'Test Project Operator',
        ]);

        $crawler = $this->client->submit($form);

        $this->assertEquals(
            'This label is already used.',
            $crawler->filter('small.error')->first()->text()
        );
    }

    public function testProjectCreateCodeIsAlreadyUsed()
    {
        $crawler = $this->client->request('GET', '/project/new');

        $form = $crawler->selectButton('Create')->form([
            'appbundle_project[label]' => 'Test1 Project1',
        ]);

        $crawler = $this->client->submit($form);

        $this->assertEquals(
            'Please select another label.',
            $crawler->filter('small.error')->first()->text()
        );
    }

    public function testProjectCreateErrorField()
    {
        $crawler = $this->client->request('GET', '/project/new');

        $form = $crawler->selectButton('Create')->form([
        ]);

        $crawler = $this->client->submit($form);

        $this->assertCount(2, $crawler->filter('small.error'));
        foreach ($crawler->filter('small.error') as $error) {
            $this->assertEquals('This value should not be blank.', $error->nodeValue);
        }
    }

    public function testProjectUpdate()
    {
        $crawler = $this->client->request('GET', '/project/TPM/edit');

        $container = static::$kernel->getContainer();

        $em = $container->get('doctrine')->getManager();

        $label    = 'Trusted Platform Module';
        $summary  = 'New Project Summury Label';
        $users    = $em->getRepository('AppBundle:User')->findAll();
        $userIds = array_map(function (User $user) {
            return $user->getId();
        }, $users);

        $form = $crawler->selectButton('Update')->form([
            'appbundle_project[label]'   => $label,
            'appbundle_project[summary]' => $summary,
            'appbundle_project[users]'   => $userIds,
        ]);

        $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirect());
        $this->assertEquals('/project/TPM', $this->client->getResponse()->headers->get('Location'));

        $projects = $em->getRepository('AppBundle:Project')->findByCode('TPM');

        $this->assertCount(1, $projects);
        $project = $projects[0];
        $this->assertEquals($label, $project->getLabel());
        $this->assertEquals($summary, $project->getSummary());
        $this->assertEquals($userIds, array_map(function (User $user) {
            return $user->getId();
        }, $project->getUsers()->toArray()));
    }

    public function testProjectUpdateLabelIsAlreadyUsed()
    {
        $crawler = $this->client->request('GET', '/project/TPM/edit');

        $form = $crawler->selectButton('Update')->form([
            'appbundle_project[label]' => 'Test Project Operator',
        ]);

        $crawler = $this->client->submit($form);

        $this->assertEquals(
            'This label is already used.',
            $crawler->filter('small.error')->first()->text()
        );
    }

    public function testProjectUpdateCodeIsAlreadyUsed()
    {
        $crawler = $this->client->request('GET', '/project/TPM/edit');

        $form = $crawler->selectButton('Update')->form([
            'appbundle_project[label]' => 'Test1 Project1',
        ]);

        $crawler = $this->client->submit($form);

        $this->assertEquals(
            'Please select another label.',
            $crawler->filter('small.error')->first()->text()
        );
    }


    public function testProjectUpdateErrorField()
    {
        $crawler = $this->client->request('GET', '/project/TPM/edit');

        $form = $crawler->selectButton('Update')->form([
            'appbundle_project[label]'   => '',
            'appbundle_project[summary]' => '',
        ]);

        $crawler = $this->client->submit($form);

        $this->assertCount(2, $crawler->filter('small.error'));
        foreach ($crawler->filter('small.error') as $error) {
            $this->assertEquals('This value should not be blank.', $error->nodeValue);
        }
    }
}
