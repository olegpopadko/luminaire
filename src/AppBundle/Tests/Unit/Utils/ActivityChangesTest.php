<?php

namespace AppBundle\Tests\Unit\Utils;

use AppBundle\Entity\Issue;
use AppBundle\Entity\IssueComment;
use AppBundle\Entity\IssueStatus;
use AppBundle\Entity\User;
use AppBundle\Tests\Unit\TestCase;
use AppBundle\Utils\ActivityChanges;

class ActivityChangesTest extends TestCase
{
    public function testGetIssueCreatedActivity()
    {
        list($tokenStorage, $user) = $this->createTokenStorageMock();

        $activityChanges = new ActivityChanges($tokenStorage);

        $issue = new Issue();

        $activity = $activityChanges->getIssueCreatedActivity($issue);

        $this->assertNull($activity->getId());
        $this->assertEquals($issue, $activity->getIssue());
        $this->assertEquals($user, $activity->getUser());
        $this->assertEquals([
            'type'         => 'issue_created',
            'entity_id'    => null,
            'entity_class' => 'AppBundle\Entity\Issue',
        ], $activity->getChanges());
    }

    public function testGetIssueCreatedActivityNull()
    {
        $tokenStorage = $this->createNullTokenStorageMock();

        $activityChanges = new ActivityChanges($tokenStorage);

        $issue = new Issue();

        $activity = $activityChanges->getIssueCreatedActivity($issue);

        $this->assertNull($activity);
    }

    public function testGetIssueCommentCreatedActivity()
    {
        list($tokenStorage, $user) = $this->createTokenStorageMock();

        $activityChanges = new ActivityChanges($tokenStorage);

        $issue        = new Issue();
        $issueComment = new IssueComment();
        $issueComment->setIssue($issue);

        $activity = $activityChanges->getIssueCommentCreatedActivity($issueComment);

        $this->assertNull($activity->getId());
        $this->assertEquals($issue, $activity->getIssue());
        $this->assertEquals($user, $activity->getUser());
        $this->assertEquals([
            'type'         => 'issue_comment_created',
            'entity_id'    => null,
            'entity_class' => 'AppBundle\Entity\IssueComment',
        ], $activity->getChanges());
    }

    public function testGetIssueCommentCreatedActivityNull()
    {
        $tokenStorage = $this->createNullTokenStorageMock();

        $activityChanges = new ActivityChanges($tokenStorage);

        $issueComment = new IssueComment();

        $activity = $activityChanges->getIssueCommentCreatedActivity($issueComment);

        $this->assertNull($activity);
    }

    public function testGetIssueStatusChangedActivity()
    {
        list($tokenStorage, $user) = $this->createTokenStorageMock();

        $activityChanges = new ActivityChanges($tokenStorage);

        $issue     = new Issue();
        $oldStatus = new IssueStatus();
        $oldStatus->setLabel('old');
        $newStatus = new IssueStatus();
        $newStatus->setLabel('new');

        $activity = $activityChanges->getIssueStatusChangedActivity($issue, $oldStatus, $newStatus);

        $this->assertNull($activity->getId());
        $this->assertEquals($issue, $activity->getIssue());
        $this->assertEquals($user, $activity->getUser());
        $this->assertEquals([
            'type'         => 'issue_status_changed',
            'old_status'   => 'old',
            'new_status'   => 'new',
            'entity_id'    => null,
            'entity_class' => 'AppBundle\Entity\Issue',
        ], $activity->getChanges());
    }

    public function testGetIssueStatusChangedActivityNull()
    {
        $tokenStorage = $this->createNullTokenStorageMock();

        $activityChanges = new ActivityChanges($tokenStorage);

        $issue = new Issue();
        $oldStatus = new IssueStatus();
        $newStatus = new IssueStatus();

        $activity = $activityChanges->getIssueStatusChangedActivity($issue, $oldStatus, $newStatus);

        $this->assertNull($activity);
    }

    private function createTokenStorageMock()
    {
        $user = new User();

        $token = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $token->expects($this->once())
            ->method('getUser')
            ->willReturn($user);

        $tokenStorage = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage');
        $tokenStorage->expects($this->once())
            ->method('getToken')
            ->willReturn($token);

        return [$tokenStorage, $user];
    }

    /**
     * @return \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage
     */
    private function createNullTokenStorageMock()
    {
        $tokenStorage = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage');
        $tokenStorage->expects($this->once())
            ->method('getToken')
            ->willReturn(null);

        return $tokenStorage;
    }
}
