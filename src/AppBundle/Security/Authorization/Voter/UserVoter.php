<?php

namespace AppBundle\Security\Authorization\Voter;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter;
use Symfony\Component\Security\Core\Authorization\Voter\RoleVoter;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * Class UserVoter
 */
class UserVoter extends AbstractVoter
{
    /**
     *
     */
    const EDIT = 'edit';

    /**
     * @var TokenStorage
     */
    private $tokenStorage;

    /**
     * @var RoleVoter
     */
    private $roleVoter;

    public function __construct(TokenStorage $tokenStorage, RoleVoter $roleVoter)
    {
        $this->tokenStorage = $tokenStorage;
        $this->roleVoter    = $roleVoter;
    }

    /**
     * @return array
     */
    protected function getSupportedAttributes()
    {
        return [self::EDIT];
    }

    /**
     * @return array
     */
    protected function getSupportedClasses()
    {
        return ['AppBundle\Entity\User'];
    }

    /**
     * @param string $attribute
     * @param object $object
     * @param null $user
     * @return bool
     */
    protected function isGranted($attribute, $object, $user = null)
    {
        $user = $this->tokenStorage->getToken()->getUser();
        switch ($attribute) {
            case self::EDIT:
                $vote = $this->roleVoter->vote($this->tokenStorage->getToken(), $user, ['ROLE_ADMIN']);
                if ($vote === VoterInterface::ACCESS_GRANTED || $user->getId() === $object->getId()) {
                    return true;
                }
                break;
            default:
                throw new \InvalidArgumentException('Unknown attribute');
        }

        return false;
    }
}
