<?php

namespace AppBundle\Security\Authorization\Voter;

use AppBundle\Entity\Issue;
use AppBundle\Entity\User;

/**
 * Class UserVoter
 */
class UserVoter extends EntityVoter
{
    /**
     * {@inheritdoc}
     */
    protected function getSupportedClass()
    {
        return 'AppBundle\Entity\User';
    }

    /**
     * {@inheritdoc}
     */
    protected function isCreateGranted(User $user)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function isViewGranted($object, User $user)
    {
        return true;
    }

    /**
     * @param Issue $object
     * @param User $user
     * @return bool
     */
    protected function isEditGranted($object, User $user)
    {
        return $user->isAdmin() || $user === $object;
    }
}
