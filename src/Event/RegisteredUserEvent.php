<?php

namespace App\Event;
use Symfony\Component\EventDispatcher\Event;
use App\Entity\User;

class RegisteredUserEvent
{
    public const NAME = 'user.register';
    /**
     * @var User $registeredUser
     */
    private $registeredUser;

    /**
     * @param User $registeredUser
     */
    public function __construct(User $registeredUser)
    {
        $this->registeredUser = $registeredUser;
    }

    /**
     * @return User
     */
    public function getRegisteredUser(): User
    {
        return  $this->registeredUser;
    }

}