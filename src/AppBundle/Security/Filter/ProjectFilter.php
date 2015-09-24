<?php

namespace AppBundle\Security\Filter;

use AppBundle\Entity\User;
use Doctrine\ORM\QueryBuilder;

class ProjectFilter extends AbstractFilter
{
    protected function supportedClass()
    {
        return 'AppBundle\Entity\Project';
    }

    protected function modifyQueryBuilder(QueryBuilder $queryBuilder, User $user)
    {
        $alias = $this->getAlias($queryBuilder);
        if (!$user->isAdmin()) {
            $userAlias = uniqid('u');
            $userParam = uniqid('user');
            $queryBuilder->innerJoin($alias . '.users', $userAlias)
                ->andWhere($userAlias . ' = :' . $userParam)
                ->setParameter($userParam, $user);
        }
        return $queryBuilder;
    }
}
