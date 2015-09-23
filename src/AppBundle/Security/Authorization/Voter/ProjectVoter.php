<?php

namespace AppBundle\Security\Authorization\Voter;

use AppBundle\Entity\User;

/**
 * Class ProjectVoter
 */
class ProjectVoter extends EntityVoter
{
    /**
     * {@inheritdoc}
     */
    protected function getSupportedClass()
    {
        return 'AppBundle\Entity\Project';
    }

    /**
     * {@inheritdoc}
     */
    protected function isCreateGranted(User $user)
    {
        return $user->isAdmin() || $user->isManager();
    }

    /**
     * {@inheritdoc}
     */
    protected function isViewGranted($object, User $user)
    {
        return $user->isAdmin() || $user->hasProject($object);
    }

    /**
     * {@inheritdoc}
     */
    protected function isEditGranted($object, User $user)
    {
        return $user->isAdmin() || $user->isManager();
    }
}
