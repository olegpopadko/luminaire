<?php

namespace AppBundle\Security\Filter;

use AppBundle\Entity\User;
use Doctrine\ORM\QueryBuilder;

/**
 * Class ActivityFilter
 */
class ActivityFilter extends AbstractFilter
{
    /**
     * @return string
     */
    protected function supportedClass()
    {
        return 'AppBundle\Entity\Activity';
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param User $user
     * @return QueryBuilder
     */
    protected function modifyQueryBuilder(QueryBuilder $queryBuilder, User $user)
    {
        $alias = $this->getAlias($queryBuilder);
        if (!$user->isAdmin()) {
            $issueAlias = uniqid('i');
            $projectAlias = uniqid('p');
            $userAlias = uniqid('u');
            $userParam = uniqid('user');
            $queryBuilder
                ->innerJoin($alias . '.issue', $issueAlias)
                ->innerJoin($issueAlias . '.project', $projectAlias)
                ->innerJoin($projectAlias . '.users', $userAlias)
                ->andWhere($userAlias . ' = :' . $userParam)
                ->setParameter($userParam, $user);
        }
        return $queryBuilder;
    }
}
