<?php

namespace AppBundle\Security\Filter;

use AppBundle\Entity\User;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class AbstractFilter
 */
abstract class AbstractFilter
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
     * @return mixed
     */
    abstract protected function supportedClass();

    /**
     * @param QueryBuilder $queryBuilder
     * @param User $user
     * @return mixed
     */
    abstract protected function modifyQueryBuilder(QueryBuilder $queryBuilder, User $user);

    /**
     * @param QueryBuilder $queryBuilder
     * @return mixed
     */
    public function apply(QueryBuilder $queryBuilder)
    {
        foreach ($queryBuilder->getRootEntities() as $rootAlias) {
            if ($this->supportedClass() === $rootAlias) {
                $user = $this->tokenStorage->getToken()->getUser();

                if (!$user instanceof UserInterface) {
                    throw new \LogicException('The current user not implements UserInterface');
                }

                if (!$user instanceof User) {
                    throw new \LogicException('The user is somehow not our User class!');
                }

                return $this->modifyQueryBuilder($queryBuilder, $user);
            }
        }
        throw new \LogicException('The QueryBuilder does not contain supported class');
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @return int|string
     */
    protected function getAlias(QueryBuilder $queryBuilder)
    {
        foreach (array_combine($queryBuilder->getRootAliases(), $queryBuilder->getRootEntities()) as $alias => $class) {
            if ($this->supportedClass() === $class) {
                return $alias;
            }
        }

        throw new \LogicException('The QueryBuilder is somehow does not contain supported class');
    }
}
