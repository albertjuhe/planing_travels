<?php

namespace App\Application\Command\Travel;

use App\Application\Command\Command;
use App\Domain\Travel\Model\Travel;
use App\Domain\User\Model\User;

class AddTravelCommand implements Command
{
    /** @var Travel */
    private $travel;
    /** @var User */
    private $user;

    /**
     * UpdateTravelCommand constructor.
     *
     * @param Travel $travel
     */
    public function __construct(Travel $travel, User $user)
    {
        $this->travel = $travel;
        $this->user = $user;
    }

    /**
     * @return Travel
     */
    public function getTravel(): Travel
    {
        return $this->travel;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }
}
