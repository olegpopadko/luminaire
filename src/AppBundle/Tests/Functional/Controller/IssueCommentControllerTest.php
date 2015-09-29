<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Tests\Functional\TestCase;

class IssueCommentControllerTest extends TestCase
{
    public function testCompleteScenario()
    {
        $operator = $this->logInOperator();

        $this->client->request('GET', '/issue/TP-1/comment/new');
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());

        /** @var \Doctrine\Common\Persistence\ObjectManager $em */
        $em = $this->get('doctrine')->getManager();

        /** @var \AppBundle\Entity\Project $project */
        $project = $em->getRepository('AppBundle:Project')->findOneByCode('TP');
        $project->addUser($operator);
        $em->persist($project);
        $em->flush();

        $this->client->request('GET', '/issue/TP-1/comment/new');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }
}
