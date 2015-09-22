<?php

namespace AppBundle\Security\Authorization\Voter;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter;
use Symfony\Component\Security\Core\Authorization\Voter\RoleVoter;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * Class ProjectVoter
 */
class ProjectVoter extends AbstractVoter
{
    /**
     *
     */
    const VIEW = 'view';

    /**
     *
     */
    const EDIT = 'edit';

    /**
     *
     */
    const CREATE = 'create';

    /**
     * @var TokenStorage
     */
    private $tokenStorage;

    /**
     * @var RoleVoter
     */
    private $roleVoter;

    /**
     * @param TokenStorage $tokenStorage
     * @param RoleVoter $roleVoter
     */
    public function __construct(TokenStorage $tokenStorage, RoleVoter $roleVoter)
    {
        $this->tokenStorage = $tokenStorage;
        $this->roleVoter    = $roleVoter;
    }

    /**
     * {@inheritdoc}
     */
    protected function getSupportedAttributes()
    {
        return [self::VIEW, self::EDIT, self::CREATE];
    }

    /**
     * {@inheritdoc}
     */
    protected function getSupportedClasses()
    {
        return ['AppBundle\Entity\Project'];
    }

    /**
     * {@inheritdoc}
     */
    protected function isGranted($attribute, $object, $user = null)
    {
        $user          = $this->tokenStorage->getToken()->getUser();
        $adminRoleVote = $this->roleVoter->vote($this->tokenStorage->getToken(), $user, ['ROLE_ADMIN']);
        if ($adminRoleVote === VoterInterface::ACCESS_GRANTED) {
            return true;
        }
        switch ($attribute) {
            case self::CREATE:
                $managerRoleVote = $this->roleVoter->vote($this->tokenStorage->getToken(), $user, ['ROLE_MANAGER']);
                if ($managerRoleVote === VoterInterface::ACCESS_GRANTED) {
                    return true;
                }
                break;
            case self::EDIT:
                $managerRoleVote = $this->roleVoter->vote($this->tokenStorage->getToken(), $user, ['ROLE_MANAGER']);
                if ($managerRoleVote === VoterInterface::ACCESS_GRANTED && $user->hasProject($object)) {
                    return true;
                }
                break;
            case self::VIEW:
                if ($user->hasProject($object)) {
                    return true;
                }
                break;
            default:
                throw new \InvalidArgumentException('Unknown attribute');
        }

        return false;
    }
}
