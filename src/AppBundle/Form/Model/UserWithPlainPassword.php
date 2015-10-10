<?php

namespace AppBundle\Form\Model;

use AppBundle\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class UserWithPlainPassword
 */
class UserWithPlainPassword
{

    /**
     * @var User
     */
    private $user;

    /**
     * @var string
     *
     * @Assert\NotBlank(groups={"not_blank_password"})
     * @Assert\Length(max=4096)
     */
    private $plainPassword;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @param string $plainPassword
     * @return $this
     */
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }
}
