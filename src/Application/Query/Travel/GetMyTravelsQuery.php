<?php

namespace App\Application\Query\Travel;

use App\Application\Query\Query;
use App\Domain\User\Model\User;

class GetMyTravelsQuery implements Query
{
    private $user;

    public function __construct(int $user)
    {
        $this->user = $user;
    }

    /**
     * @return User
     */
    public function getUser(): int
    {
        return $this->user;
    }
}
