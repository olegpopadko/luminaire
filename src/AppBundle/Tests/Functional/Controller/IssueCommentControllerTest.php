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

        $crawler = $this->client->request('GET', '/issue/TP-1/comment/new');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $body = 'Content message';

        $form = $crawler->selectButton('Create')->form([
            'appbundle_issue_comment[body]' => $body,
        ]);

        $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirect());
        $this->assertEquals('/issue/TP-1', $this->client->getResponse()->headers->get('Location'));

        $comments = $em->getRepository('AppBundle:IssueComment')->findBy(['body' => $body]);

        $this->assertCount(1, $comments);
        $comment = $comments[0];
        $this->assertEquals($operator->getId(), $comment->getUser()->getId());
        $issue = $em->getRepository('AppBundle:Issue')->findOneBy(['code' => 1, 'project' => $project]);
        $this->assertNotNull($issue);
        $this->assertEquals($issue, $comment->getIssue());
        $this->assertNull($comment->getParent());

        $this->client->request('GET', '/comment/' . $comment->getId() . '/edit');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }
}
