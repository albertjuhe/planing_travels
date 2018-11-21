<?php
/**
 * Created by PhpStorm.
 * User: albert.juhe
 * Date: 09/11/2018
 * Time: 17:30
 */

namespace App\Application\Command\User;


use App\Domain\User\Model\User;

class SignInUserCommand
{
    /** @var User */
    private $user;

    /**
     * SignUpCommand constructor.
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