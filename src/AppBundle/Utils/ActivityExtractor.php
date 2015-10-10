<?php

namespace AppBundle\Utils;

use AppBundle\Entity\Issue;
use AppBundle\Entity\Project;
use AppBundle\Entity\User;
use AppBundle\Security\Filter\ActivityFilter;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\QueryBuilder;

/**
 * Class ActivityExtractor
 */
class ActivityExtractor
{
    /**
     * @var ObjectManager
     */
    private $manager;

    /**
     * @var QueryBuilder
     */
    private $builder;

    /**
     * @param QueryBuilder $builder
     */
    public function __construct(ObjectManager $manager, ActivityFilter $activityFilter)
    {
        $this->manager = $manager;
        $this->init($activityFilter);
    }

    /**
     * @param ActivityFilter $activityFilter
     */
    private function init(ActivityFilter $activityFilter)
    {
        $this->builder = $this->manager->getRepository('AppBundle:Activity')->createQueryBuilder('a');
        $this->builder
            ->select('a', 'i', 'p')
            ->innerJoin('a.issue', 'i')
            ->innerJoin('i.project', 'p')
            ->orderBy('a.createdAt', 'DESC')
            ->orderBy('a.id', 'DESC');

        $activityFilter->apply($this->builder);
    }

    /**
     * @param Issue $issue
     */
    public function whereIssue(Issue $issue)
    {
        if (!is_null($issue)) {
            $this->isIssue = true;
            $this->builder->andWhere('i = :issue')->setParameter('issue', $issue);
        }
        return $this;
    }

    /**
     * @param Project $project
     */
    public function whereProject(Project $project)
    {
        if (!is_null($project)) {
            $this->builder->andWhere('p = :project')->setParameter('project', $project);
        }
        return $this;
    }

    /**
     * @param User $user
     */
    public function whereUserIsAssigned(User $user)
    {
        if (!is_null($user)) {
            $this->builder->andWhere('i.assignee = :user')->setParameter('user', $user);
        }
        return $this;
    }

    /**
     * @param User $user
     */
    public function whereUserIsAuthor(User $user)
    {
        if (!is_null($user)) {
            $this->builder->andWhere('a.user = :author')->setParameter('author', $user);
        }
        return $this;
    }

    /**
     * @param User $user
     */
    public function whereUserIsMember(User $user)
    {
        if (!is_null($user)) {
            $this->builder->innerJoin('p.users', 'u')->andWhere('u = :member')->setParameter('member', $user);
        }
        return $this;
    }

    /**
     * @return $this
     */
    public function onlyOpenedIssue()
    {
        $this->builder->andWhere('i.status not in  (:statuses)')
            ->setParameter(
                'statuses',
                [
                    $this->manager->getRepository('AppBundle:IssueStatus')->findClosed(),
                    $this->manager->getRepository('AppBundle:IssueStatus')->findResolved(),
                ]
            );
        return $this;
    }

    /**
     * @param $maxResults
     */
    public function setMaxResults($maxResults)
    {
        $this->builder->setMaxResults($maxResults);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getResults()
    {
        return $this->builder->getQuery()->execute();
    }
}
