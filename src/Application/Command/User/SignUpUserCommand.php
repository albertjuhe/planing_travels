<?php

namespace App\Application\Command\User;

use App\Application\Command\Command;
use App\Domain\User\Model\User;

class SignUpUserCommand implements Command
{
    /** @var User */
    private $user;

    /**
     * SignUpCommand constructor.
     *
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
