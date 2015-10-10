<?php

namespace AppBundle\Security\Authorization\Voter;

use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class EntityVoter
 */
abstract class EntityVoter extends \Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter
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
     * {@inheritdoc}
     */
    protected function getSupportedAttributes()
    {
        return [$this->getCreateAttributeName(), self::VIEW, self::EDIT];
    }

    /**
     * {@inheritdoc}
     */
    protected function getSupportedClasses()
    {
        return [$this->getSupportedClass()];
    }

    /**
     * @return string
     */
    abstract protected function getSupportedClass();

    /**
     * @param TokenInterface $token
     * @param object $object
     * @param array $attributes
     * @return int
     */
    public function vote(TokenInterface $token, $object, array $attributes)
    {
        if (!is_null($object) && !(is_object($object) && $this->supportsClass(get_class($object)))) {
            return self::ACCESS_ABSTAIN;
        }

        // abstain vote by default in case none of the attributes are supported
        $vote = self::ACCESS_ABSTAIN;

        foreach ($attributes as $attribute) {
            if (!$this->supportsAttribute($attribute)) {
                continue;
            }

            // as soon as at least one attribute is supported, default is to deny access
            $vote = self::ACCESS_DENIED;

            if ($this->isGranted($attribute, $object, $token->getUser())) {
                // grant access as soon as at least one voter returns a positive response
                return self::ACCESS_GRANTED;
            }
        }

        return $vote;
    }

    /**
     * {@inheritdoc}
     */
    protected function isGranted($attribute, $object, $user = null)
    {
        if (!$user instanceof UserInterface) {
            return false;
        }

        if (!$user instanceof User) {
            throw new \LogicException('The user is somehow not our User class!');
        }

        switch ($attribute) {
            case $this->getCreateAttributeName():
                return $this->isCreateGranted($user);
            case self::VIEW:
                return $this->isViewGranted($object, $user);
            case self::EDIT:
                return $this->isEditGranted($object, $user);
        }

        return false;
    }

    /**
     * @return string
     */
    protected function getCreateAttributeName()
    {
        $parts = explode('\\', $this->getSupportedClass());
        if (count($parts) === 0) {
            throw new \LogicException('Wrong getSupportedClass implementation!');
        }
        return 'create_' . strtolower($parts[count($parts) - 1]);
    }

    /**
     * @param User $user
     * @return boolean
     */
    abstract protected function isCreateGranted(User $user);

    /**
     * @param $object
     * @param User $user
     * @return boolean
     */
    abstract protected function isViewGranted($object, User $user);

    /**
     * @param $object
     * @param User $user
     * @return boolean
     */
    abstract protected function isEditGranted($object, User $user);
}
