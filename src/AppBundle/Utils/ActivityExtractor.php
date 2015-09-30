<?php

namespace AppBundle\Utils;

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
     * @var QueryBuilder
     */
    private $builder;

    /**
     * @param QueryBuilder $builder
     */
    public function __construct(ObjectManager $manager, ActivityFilter $activityFilter)
    {
        $this->builder = $manager->getRepository('AppBundle:Activity')->createQueryBuilder('a');
        $this->builder
            ->select('a', 'i', 'p')
            ->innerJoin('a.issue', 'i')
            ->innerJoin('i.project', 'p');

        $activityFilter->apply($this->builder);
    }

    /**
     * @param Project $project
     */
    public function whereProject(Project $project)
    {
        if (!is_null($project)) {
            $this->builder->andWhere('p = :project')->setParameter('project', $project);
        }
    }

    /**
     * @param User $user
     */
    public function whereUserIsAssigned(User $user)
    {
        if (!is_null($user)) {
            $this->builder->andWhere('i.assignee = :user')->setParameter('user', $user);
        }
    }

    /**
     * @param User $user
     */
    public function whereUserIsMember(User $user)
    {
        if (!is_null($user)) {
            $this->builder->innerJoin('p.users', 'u')->andWhere('u = :member')->setParameter('member', $user);
        }
    }

    /**
     * @param $maxResults
     */
    public function setMaxResults($maxResults)
    {
        $this->builder->setMaxResults($maxResults);
    }

    /**
     * @return mixed
     */
    public function getResults()
    {
        return $this->builder->getQuery()->execute();
    }
}
