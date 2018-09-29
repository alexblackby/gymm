<?php

namespace AppBundle\Event;


use AppBundle\Entity\User;
use Symfony\Component\EventDispatcher\Event;

class UserSignupEvent extends Event
{
    const NAME = 'user.signup';

    /**
     * @var User
     */
    private $user;

    /**
     * UserSignupEvent constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }


}