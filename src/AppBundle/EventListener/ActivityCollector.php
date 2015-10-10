<?php

namespace AppBundle\EventListener;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use AppBundle\Event\ActivityEvent;
use AppBundle\Utils\ActivityChanges;
use AppBundle\Entity\Issue;
use AppBundle\Entity\IssueComment;

/**
 * Class ActivityCollector
 */
class ActivityCollector
{
    /**
     * @var ActivityChanges
     */
    private $activityChanges;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param ActivityChanges $activityChanges
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(ActivityChanges $activityChanges, EventDispatcherInterface $eventDispatcher)
    {
        $this->activityChanges = $activityChanges;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param $entity
     * @param LifecycleEventArgs $args
     * @return null
     */
    public function postPersist($entity, LifecycleEventArgs $args)
    {
        if (!$this->supportedEnvironmentOnPostPersist($entity)) {
            return null;
        }

        $activity = null;

        if ($entity instanceof Issue) {
            $activity = $this->activityChanges->getIssueCreatedActivity($entity);
        }

        if ($entity instanceof IssueComment) {
            $activity = $this->activityChanges->getIssueCommentCreatedActivity($entity);
        }

        if ($activity) {
            $em = $args->getEntityManager();
            $em->persist($activity);
            $em->flush();
            $this->dispatchEvent($activity);
        }
    }

    /**
     * @param $activity
     */
    private function dispatchEvent($activity)
    {
        $this->eventDispatcher->dispatch('app.events.activity_created', new ActivityEvent($activity));
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

        $activity = $this->activityChanges->getIssueStatusChangedActivity(
            $entity,
            $args->getOldValue('status'),
            $args->getNewValue('status')
        );

        if ($activity) {
            $em = $args->getEntityManager();
            $em->persist($activity);
            $this->dispatchEvent($activity);
        }
    }

    /**
     * @param $entity
     */
    private function supportedEnvironmentOnPostPersist($entity)
    {
        return $entity instanceof Issue || $entity instanceof IssueComment;
    }

    /**
     * @param $entity
     */
    private function supportedEnvironmentOnPreUpdate(PreUpdateEventArgs $args)
    {
        return $args->getEntity() instanceof Issue && $args->hasChangedField('status');
    }
}
