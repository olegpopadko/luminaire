<?php

namespace AppBundle\Utils;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\User\UserInterface;
use AppBundle\Entity\Activity;
use AppBundle\Entity\Issue;
use AppBundle\Entity\IssueComment;
use AppBundle\Entity\IssueStatus;
use AppBundle\Entity\User;

/**
 * Class ActivityChanges
 */
class ActivityChanges
{
    /**
     * @return string
     */
    private function getIssueCreatedType()
    {
        return 'issue_created';
    }

    /**
     * @return string
     */
    private function getIssueCommentCreatedType()
    {
        return 'issue_comment_created';
    }

    /**
     * @return string
     */
    private function getIssueStatusChangedType()
    {
        return 'issue_status_changed';
    }

    /**
     * @var TokenStorage
     */
    private $tokenStorage;

    /**
     * @param TokenStorage $tokenStorage
     */
    public function __construct(TokenStorage $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param Issue $issue
     * @return Activity
     */
    public function getIssueCreatedActivity(Issue $issue)
    {
        if (!$user = $this->getCurrentUser()) {
            return null;
        }

        $entity = new Activity();
        $entity->setIssue($issue);
        $entity->setUser($user);
        $entity->setChanges([
            'type'         => $this->getIssueCreatedType(),
            'entity_id'    => $issue->getId(),
            'entity_class' => get_class($issue),
        ]);
        return $entity;
    }

    /**
     * @param IssueComment $issueComment
     * @return Activity
     */
    public function getIssueCommentCreatedActivity(IssueComment $issueComment)
    {
        if (!$user = $this->getCurrentUser()) {
            return null;
        }

        $entity = new Activity();
        $entity->setIssue($issueComment->getIssue());
        $entity->setUser($user);
        $entity->setChanges([
            'type'         => $this->getIssueCommentCreatedType(),
            'entity_id'    => $issueComment->getId(),
            'entity_class' => get_class($issueComment),
        ]);
        return $entity;
    }

    /**
     * @param Issue $issue
     * @param $oldStatus
     * @param $newStatus
     * @return Activity
     */
    public function getIssueStatusChangedActivity(Issue $issue, IssueStatus $oldStatus, IssueStatus $newStatus)
    {
        if (!$user = $this->getCurrentUser()) {
            return null;
        }

        $entity = new Activity();
        $entity->setIssue($issue);
        $entity->setUser($user);
        $entity->setChanges([
            'type'         => $this->getIssueStatusChangedType(),
            'old_status'   => $oldStatus->getLabel(),
            'new_status'   => $newStatus->getLabel(),
            'entity_id'    => $issue->getId(),
            'entity_class' => get_class($issue),
        ]);
        return $entity;
    }

    /**
     * @return mixed
     */
    private function getCurrentUser()
    {
        $token = $this->tokenStorage->getToken();
        if (is_null($token)) {
            return null;
        }

        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            throw new \LogicException('The current user not implements UserInterface');
        }

        if (!$user instanceof User) {
            throw new \LogicException('The user is somehow not our User class!');
        }

        return $user;
    }
}
