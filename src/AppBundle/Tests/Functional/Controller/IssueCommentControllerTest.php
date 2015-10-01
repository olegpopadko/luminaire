<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Tests\Functional\TestCase;

class IssueCommentControllerTest extends TestCase
{
    public function testCompleteScenario()
    {
        $operator = $this->logInOperator();

        $em = $this->getManager();

        $crawler = $this->client->request('GET', '/issue/TP-2/comment/new');
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $body = 'Content message';

        $form = $crawler->selectButton('Create')->form([
            'appbundle_issue_comment[body]' => $body,
        ]);

        $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirect());
        $this->assertEquals('/issue/TP-2', $this->client->getResponse()->headers->get('Location'));

        $comments = $em->getRepository('AppBundle:IssueComment')->findBy(['body' => $body]);

        $this->assertCount(1, $comments);
        $comment = $comments[0];
        $this->assertEquals($operator->getId(), $comment->getUser()->getId());
        $issue = $issue = $this->get('app.issue_code_converter')->find('TP-2');
        $this->assertNotNull($issue);
        $this->assertEquals($issue->getId(), $comment->getIssue()->getId());
        $this->assertNull($comment->getParent());

        $activity = $this->getManager()->getRepository('AppBundle:Activity')->createQueryBuilder('a')
            ->orderBy('a.id', 'DESC')->getQuery()->setMaxResults(1)->getOneOrNullResult();
        $this->assertEquals('operator', $activity->getUser()->getUsername());
        $this->assertEquals([
            'type'         => 'issue_comment_created',
            'entity_id'    => $comment->getId(),
            'entity_class' => get_class($comment),
        ], $activity->getChanges());

        $this->assertCount(2, $issue->getCollaborators());
        $operator  = $this->getReference('operator-user');
        $operator1 = $this->getReference('operator1-user');
        $this->assertEquals($operator->getId(), $issue->getCollaborators()->last()->getId());
        $this->assertEquals($operator1->getId(), $issue->getCollaborators()->first()->getId());

        $this->client->request('GET', '/comment/' . $comment->getId() . '/edit');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }
}
