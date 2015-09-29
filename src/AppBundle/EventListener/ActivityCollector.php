<?php

namespace AppBundle\EventListener;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use AppBundle\Entity\Activity;
use AppBundle\Entity\Issue;
use AppBundle\Entity\IssueComment;
use AppBundle\Entity\User;

/**
 * Class ActivityCollector
 */
class ActivityCollector
{
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
     * @param $entity
     * @param LifecycleEventArgs $args
     * @return null
     */
    public function postPersist($entity, LifecycleEventArgs $args)
    {
        if (!$this->supportedEnvironmentOnPrePersist($entity)) {
            return null;
        }

        $em = $args->getEntityManager();

        $issue = $entity;
        $type  = 'issue_created';
        if ($entity instanceof IssueComment) {
            $issue = $entity->getIssue();
            $type  = 'comment_created';
        }

        $activity = new Activity();
        $activity->setIssue($issue);
        $activity->setUser($this->getCurrentUser());
        $activity->setChanges([
            'type'         => $type,
            'entity_id'    => $entity->getId(),
            'entity_class' => get_class($entity),
        ]);

        $em->persist($activity);
        $em->flush();
    }

    /**
     * @param Issue $entity
     * @param LifecycleEventArgs $args
     * @return null
     */
    public function preUpdate($entity, PreUpdateEventArgs $args)
    {
        if (!$this->supportedEnvironmentOnPreUpdate($args)) {
            return null;
        }

        $em       = $args->getEntityManager();
        $activity = new Activity();
        $activity->setIssue($entity);
        $activity->setUser($this->getCurrentUser());
        $activity->setChanges([
            'type'         => 'issue_status_changed',
            'old_status'   => $args->getOldValue('status')->getLabel(),
            'new_status'   => $args->getNewValue('status')->getLabel(),
            'entity_id'    => $entity->getId(),
            'entity_class' => get_class($entity),
        ]);

        $em->persist($activity);
        $em->flush();
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

    /**
     * @param $entity
     */
    private function supportedEnvironmentOnPrePersist($entity)
    {
        return ($entity instanceof Issue || $entity instanceof IssueComment) && $this->getCurrentUser();
    }

    /**
     * @param $entity
     */
    private function supportedEnvironmentOnPreUpdate(PreUpdateEventArgs $args)
    {
        return $args->getEntity() instanceof Issue && $this->getCurrentUser() && $args->hasChangedField('status');
    }
}
