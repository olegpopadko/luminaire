<?php

namespace AppBundle\Security\Authorization\Voter;

use AppBundle\Entity\Issue;
use AppBundle\Entity\User;

/**
 * Class IssueVoter
 */
class IssueVoter extends EntityVoter
{
    /**
     * {@inheritdoc}
     */
    protected function getSupportedClass()
    {
        return 'AppBundle\Entity\Issue';
    }

    /**
     * {@inheritdoc}
     */
    protected function isCreateGranted(User $user)
    {
        return true;
    }

    /**
     * @param Issue $object
     * @param User $user
     * @return bool
     */
    protected function isViewGranted($object, User $user)
    {
        return $user->hasProject($object->getProject());
    }

    /**
     * @param Issue $object
     * @param User $user
     * @return bool
     */
    protected function isEditGranted($object, User $user)
    {
        return $user->hasProject($object->getProject());
    }
}
