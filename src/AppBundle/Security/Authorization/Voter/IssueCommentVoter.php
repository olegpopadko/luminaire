<?php

namespace AppBundle\Security\Authorization\Voter;

use AppBundle\Entity\IssueComment;
use AppBundle\Entity\User;

/**
 * Class IssueCommentVoter
 */
class IssueCommentVoter extends EntityVoter
{
    /**
     * {@inheritdoc}
     */
    protected function getSupportedClass()
    {
        return 'AppBundle\Entity\IssueComment';
    }

    /**
     * {@inheritdoc}
     */
    protected function isCreateGranted(User $user)
    {
        return true;
    }

    /**
     * @param IssueComment $object
     * @param User $user
     * @return bool
     */
    protected function isViewGranted($object, User $user)
    {
        return $user->isAdmin() || $user->hasProject($object->getIssue()->getProject());
    }

    /**
     * @param IssueComment $object
     * @param User $user
     * @return bool
     */
    protected function isEditGranted($object, User $user)
    {
        return $user->isAdmin() || $object->getUser() === $user;
    }
}
