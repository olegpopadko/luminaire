<?php

namespace AppBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use AppBundle\Entity\Issue;
use AppBundle\Entity\IssueComment;

/**
 * Class IssueCollaborator
 */
class IssueCollaborator
{
    /**
     * @param $entity
     * @param LifecycleEventArgs $args
     */
    public function postPersist($entity, LifecycleEventArgs $args)
    {
        $this->updateCollaborators($entity, $args);
    }

    /**
     * @param $entity
     * @param LifecycleEventArgs $args
     */
    public function postUpdate($entity, LifecycleEventArgs $args)
    {
        $this->updateCollaborators($entity, $args);
    }

    /**
     * @param $entity
     * @param LifecycleEventArgs $args
     * @return null
     */
    private function updateCollaborators($entity, LifecycleEventArgs $args)
    {
        if (!$this->supportsEntity($entity)) {
            return null;
        }

        if ($entity instanceof Issue) {
            $this->issueProcess($entity, $args);
        }

        if ($entity instanceof IssueComment) {
            $this->issueCommentProcess($entity, $args);
        }
    }

    /**
     * @param Issue $entity
     * @param LifecycleEventArgs $args
     */
    private function issueProcess(Issue $entity, LifecycleEventArgs $args)
    {
        $entity->addCollaborator($entity->getReporter());

        if ($assignee = $entity->getAssignee()) {
            $entity->addCollaborator($assignee);
        }

        $em = $args->getEntityManager();
        $em->persist($entity);
        $em->flush();
    }

    /**
     * @param IssueComment $entity
     * @param LifecycleEventArgs $args
     */
    private function issueCommentProcess(IssueComment $entity, LifecycleEventArgs $args)
    {
        $issue = $entity->getIssue();
        $issue->addCollaborator($entity->getUser());

        $em = $args->getEntityManager();
        $em->persist($issue);
        $em->flush();
    }

    /**
     * @param $entity
     * @return bool
     */
    private function supportsEntity($entity)
    {
        return $entity instanceof Issue || $entity instanceof IssueComment;
    }
}
