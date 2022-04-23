<?php

namespace App\Application\Query\Travel;

use App\Application\Query\Query;
use App\Domain\User\Model\User;
use Symfony\Component\Security\Core\User\UserInterface;

class GetMyTravelsQuery implements Query
{
    private $user;

    public function __construct(UserInterface $user)
    {
        $this->user = $user;
    }

    /**
     * @return User
     */
    public function getUser(): UserInterface
    {
        return $this->user;
    }
}
