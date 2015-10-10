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

        $issue = $this->getMock('AppBundle\Entity\Issue');
        $issue->expects($this->once())
            ->method('getId')
            ->willReturn(1);

        $activity = $activityChanges->getIssueCreatedActivity($issue);

        $this->assertNull($activity->getId());
        $this->assertEquals($issue, $activity->getIssue());
        $this->assertEquals($user, $activity->getUser());
        $this->assertEquals([
            'type'         => 'issue_created',
            'entity_id'    => 1,
            'entity_class' => get_class($issue),
        ], $activity->getChanges());
    }

    public function testGetIssueCreatedActivityNull()
    {
        $tokenStorage = $this->createNullTokenStorageMock();

        $activityChanges = new ActivityChanges($tokenStorage);

        $activity = $activityChanges->getIssueCreatedActivity($this->getMock('AppBundle\Entity\Issue'));

        $this->assertNull($activity);
    }

    public function testGetIssueCommentCreatedActivity()
    {
        $user         = $this->getMock('AppBundle\Entity\User');
        $tokenStorage = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage');

        $activityChanges = new ActivityChanges($tokenStorage);

        $issue        = $this->getMock('AppBundle\Entity\Issue');
        $issueComment = $this->getMock('AppBundle\Entity\IssueComment');
        $issueComment->expects($this->once())
            ->method('getId')
            ->willReturn(1);

        $issueComment->expects($this->once())
            ->method('getUser')
            ->willReturn($user);

        $issueComment->expects($this->once())
            ->method('getIssue')
            ->willReturn($issue);

        $activity = $activityChanges->getIssueCommentCreatedActivity($issueComment);

        $this->assertNull($activity->getId());
        $this->assertEquals($issue, $activity->getIssue());
        $this->assertEquals($user, $activity->getUser());
        $this->assertEquals([
            'type'         => 'issue_comment_created',
            'entity_id'    => 1,
            'entity_class' => get_class($issueComment),
        ], $activity->getChanges());
    }

    public function testGetIssueStatusChangedActivity()
    {
        list($tokenStorage, $user) = $this->createTokenStorageMock();

        $activityChanges = new ActivityChanges($tokenStorage);

        $issue = $this->getMock('AppBundle\Entity\Issue');
        $issue->expects($this->once())
            ->method('getId')
            ->willReturn(10);

        $oldStatus = $this->getMock('AppBundle\Entity\IssueStatus');
        $oldStatus->expects($this->once())
            ->method('getLabel')
            ->willReturn('old');

        $newStatus = $this->getMock('AppBundle\Entity\IssueStatus');
        $newStatus->expects($this->once())
            ->method('getLabel')
            ->willReturn('new');

        $activity = $activityChanges->getIssueStatusChangedActivity($issue, $oldStatus, $newStatus);

        $this->assertNull($activity->getId());
        $this->assertEquals($issue, $activity->getIssue());
        $this->assertEquals($user, $activity->getUser());
        $this->assertEquals([
            'type'         => 'issue_status_changed',
            'old_status'   => 'old',
            'new_status'   => 'new',
            'entity_id'    => 10,
            'entity_class' => get_class($issue),
        ], $activity->getChanges());
    }

    public function testGetIssueStatusChangedActivityNull()
    {
        $tokenStorage = $this->createNullTokenStorageMock();

        $activityChanges = new ActivityChanges($tokenStorage);

        $issue     = $this->getMock('AppBundle\Entity\Issue');
        $oldStatus = $this->getMock('AppBundle\Entity\IssueStatus');
        $newStatus = $this->getMock('AppBundle\Entity\IssueStatus');

        $activity = $activityChanges->getIssueStatusChangedActivity($issue, $oldStatus, $newStatus);

        $this->assertNull($activity);
    }

    private function createTokenStorageMock()
    {
        $user = $this->getMock('AppBundle\Entity\User');

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
