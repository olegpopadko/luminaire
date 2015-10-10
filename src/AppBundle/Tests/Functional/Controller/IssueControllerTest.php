<?php

namespace AppBundle\Tests\Functional\Controller\Admin;

use AppBundle\Tests\Functional\TestCase;

class IssueControllerTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->logInOperator();
    }

    public function testIssueCreate()
    {
        $crawler = $this->client->request('GET', '/project/TP/issue/new');

        $em = $this->getManager();

        $summary     = 'Test manager issue summary';
        $description = 'Test manager issue description';
        $assignee    = $this->getReference('operator-user');
        $reporter    = $this->getReference('operator-user');
        $status      = $this->getReference('open-issue-status');
        $priority    = $this->getReference('major-issue-priority');
        $type        = $this->getReference('task-issue-type');

        $form = $crawler->selectButton('Create')->form([
            'appbundle_issue[summary]'     => $summary,
            'appbundle_issue[description]' => $description,
            'appbundle_issue[assignee]'    => $assignee->getId(),
            'appbundle_issue[reporter]'    => $reporter->getId(),
            'appbundle_issue[status]'      => $status->getId(),
            'appbundle_issue[priority]'    => $priority->getId(),
            'appbundle_issue[type]'        => $type->getId(),
        ]);

        $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirect());
        $this->assertEquals('/issue/TP-3', $this->client->getResponse()->headers->get('Location'));

        $issues = $em->getRepository('AppBundle:Issue')->findByCode(3);

        $this->assertCount(1, $issues);
        $issue = $issues[0];
        $this->assertEquals($summary, $issue->getSummary());
        $this->assertEquals($description, $issue->getDescription());
        $this->assertEquals('TP', $issue->getProject()->getCode());
        $this->assertEquals($assignee, $issue->getAssignee());
        $this->assertEquals($reporter, $issue->getReporter());
        $this->assertEquals($status, $issue->getStatus());
        $this->assertEquals($priority, $issue->getPriority());
        $this->assertEquals($type, $issue->getType());

        $activity = $this->getManager()->getRepository('AppBundle:Activity')->createQueryBuilder('a')
            ->orderBy('a.id', 'DESC')->getQuery()->setMaxResults(1)->getOneOrNullResult();
        $this->assertEquals('operator', $activity->getUser()->getUsername());
        $this->assertEquals([
            'type'         => 'issue_created',
            'entity_id'    => $issue->getId(),
            'entity_class' => get_class($issue),
        ], $activity->getChanges());
    }

    public function testIssueCreateSubtask()
    {
        $crawler = $this->client->request('GET', '/project/TP/issue/new');

        $summary     = 'Test manager issue summary';
        $description = 'Test manager issue description';
        $assignee    = $this->getReference('operator-user');
        $reporter    = $this->getReference('operator-user');
        $status      = $this->getReference('open-issue-status');
        $priority    = $this->getReference('major-issue-priority');
        $type        = $this->getReference('subtask-issue-type');

        $form = $crawler->selectButton('Create')->form([
            'appbundle_issue[summary]'     => $summary,
            'appbundle_issue[description]' => $description,
            'appbundle_issue[assignee]'    => $assignee->getId(),
            'appbundle_issue[reporter]'    => $reporter->getId(),
            'appbundle_issue[status]'      => $status->getId(),
            'appbundle_issue[priority]'    => $priority->getId(),
            'appbundle_issue[type]'        => $type->getId(),
        ]);

        $crawler = $this->client->submit($form);

        $this->assertEquals(
            'Parent issue must be set for Subtask issue.',
            $crawler->filter('small.error')->first()->text()
        );
    }

    public function testIssueCreateResolved()
    {
        $crawler = $this->client->request('GET', '/project/TP/issue/new');

        $summary     = 'Test manager issue summary';
        $description = 'Test manager issue description';
        $assignee    = $this->getReference('operator-user');
        $reporter    = $this->getReference('operator-user');
        $status      = $this->getReference('resolved-issue-status');
        $priority    = $this->getReference('major-issue-priority');
        $type        = $this->getReference('task-issue-type');

        $form = $crawler->selectButton('Create')->form([
            'appbundle_issue[summary]'     => $summary,
            'appbundle_issue[description]' => $description,
            'appbundle_issue[assignee]'    => $assignee->getId(),
            'appbundle_issue[reporter]'    => $reporter->getId(),
            'appbundle_issue[status]'      => $status->getId(),
            'appbundle_issue[priority]'    => $priority->getId(),
            'appbundle_issue[type]'        => $type->getId(),
        ]);

        $crawler = $this->client->submit($form);

        $this->assertEquals(
            'Resolution must be set for Resolved issue.',
            $crawler->filter('small.error')->first()->text()
        );
    }

    public function testIssueCreateErrorField()
    {
        $crawler = $this->client->request('GET', '/project/TP/issue/new');

        $form = $crawler->selectButton('Create')->form([
        ]);

        $crawler = $this->client->submit($form);

        $this->assertCount(2, $crawler->filter('small.error'));
        foreach ($crawler->filter('small.error') as $error) {
            $this->assertEquals('This value should not be blank.', $error->nodeValue);
        }
    }

    public function testIssueUpdate()
    {
        $crawler = $this->client->request('GET', '/issue/TP-1/edit');

        $summary     = 'Test manager issue summary';
        $description = 'Test manager issue description';
        $assignee    = $this->getReference('operator1-user');
        $reporter    = $this->getReference('operator1-user');
        $status      = $this->getReference('closed-issue-status');
        $priority    = $this->getReference('critical-issue-priority');
        $type        = $this->getReference('story-issue-type');

        $form = $crawler->selectButton('Update')->form([
            'appbundle_issue[summary]'     => $summary,
            'appbundle_issue[description]' => $description,
            'appbundle_issue[assignee]'    => $assignee->getId(),
            'appbundle_issue[reporter]'    => $reporter->getId(),
            'appbundle_issue[status]'      => $status->getId(),
            'appbundle_issue[priority]'    => $priority->getId(),
            'appbundle_issue[type]'        => $type->getId(),
        ]);

        $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirect());
        $this->assertEquals('/issue/TP-1', $this->client->getResponse()->headers->get('Location'));

        $issue = $this->get('app.issue_code_converter')->find('TP-1');

        $this->assertEquals($summary, $issue->getSummary());
        $this->assertEquals($description, $issue->getDescription());
        $this->assertEquals('TP', $issue->getProject()->getCode());
        $this->assertEquals($assignee->getId(), $issue->getAssignee()->getId());
        $this->assertEquals($reporter->getId(), $issue->getReporter()->getId());
        $this->assertEquals($status->getId(), $issue->getStatus()->getId());
        $this->assertEquals($priority->getId(), $issue->getPriority()->getId());
        $this->assertEquals($type->getId(), $issue->getType()->getId());
        $this->assertCount(2, $issue->getCollaborators());
        $operator  = $this->getReference('operator-user');
        $operator1 = $this->getReference('operator1-user');
        $this->assertEquals($operator->getId(), $issue->getCollaborators()->first()->getId());
        $this->assertEquals($operator1->getId(), $issue->getCollaborators()->last()->getId());

        $activity = $this->getManager()->getRepository('AppBundle:Activity')->createQueryBuilder('a')
            ->orderBy('a.id', 'DESC')->getQuery()->setMaxResults(1)->getOneOrNullResult();

        $this->assertEquals('operator', $activity->getUser()->getUsername());
        $this->assertEquals([
            'type'         => 'issue_status_changed',
            'old_status'   => 'Open',
            'new_status'   => 'Closed',
            'entity_id'    => $issue->getId(),
            'entity_class' => get_class($issue),
        ], $activity->getChanges());
    }

    public function testIssueUpdateSubtask()
    {
        $crawler = $this->client->request('GET', '/issue/TP-1/edit');

        $summary     = 'Test manager issue summary';
        $description = 'Test manager issue description';
        $assignee    = $this->getReference('operator-user');
        $reporter    = $this->getReference('operator-user');
        $status      = $this->getReference('open-issue-status');
        $priority    = $this->getReference('major-issue-priority');
        $type        = $this->getReference('subtask-issue-type');

        $form = $crawler->selectButton('Update')->form([
            'appbundle_issue[summary]'     => $summary,
            'appbundle_issue[description]' => $description,
            'appbundle_issue[assignee]'    => $assignee->getId(),
            'appbundle_issue[reporter]'    => $reporter->getId(),
            'appbundle_issue[status]'      => $status->getId(),
            'appbundle_issue[priority]'    => $priority->getId(),
            'appbundle_issue[type]'        => $type->getId(),
        ]);

        $crawler = $this->client->submit($form);

        $this->assertEquals(
            'Parent issue must be set for Subtask issue.',
            $crawler->filter('small.error')->first()->text()
        );
    }

    public function testIssueUpdateResolved()
    {
        $crawler = $this->client->request('GET', '/issue/TP-1/edit');

        $summary     = 'Test manager issue summary';
        $description = 'Test manager issue description';
        $assignee    = $this->getReference('operator-user');
        $reporter    = $this->getReference('operator-user');
        $status      = $this->getReference('resolved-issue-status');
        $priority    = $this->getReference('major-issue-priority');
        $type        = $this->getReference('task-issue-type');

        $form = $crawler->selectButton('Update')->form([
            'appbundle_issue[summary]'     => $summary,
            'appbundle_issue[description]' => $description,
            'appbundle_issue[assignee]'    => $assignee->getId(),
            'appbundle_issue[reporter]'    => $reporter->getId(),
            'appbundle_issue[status]'      => $status->getId(),
            'appbundle_issue[priority]'    => $priority->getId(),
            'appbundle_issue[type]'        => $type->getId(),
        ]);

        $crawler = $this->client->submit($form);

        $this->assertEquals(
            'Resolution must be set for Resolved issue.',
            $crawler->filter('small.error')->first()->text()
        );
    }

    public function testIssueUpdateErrorField()
    {
        $crawler = $this->client->request('GET', '/issue/TP-1/edit');

        $form = $crawler->selectButton('Update')->form([
            'appbundle_issue[summary]'     => '',
            'appbundle_issue[description]' => '',
        ]);

        $crawler = $this->client->submit($form);

        $this->assertCount(2, $crawler->filter('small.error'));
        foreach ($crawler->filter('small.error') as $error) {
            $this->assertEquals('This value should not be blank.', $error->nodeValue);
        }
    }
}
