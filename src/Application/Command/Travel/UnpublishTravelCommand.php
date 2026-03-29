<?php

namespace App\Application\Command\Travel;

use App\Application\Command\Command;
use App\Domain\User\Model\User;

class UnpublishTravelCommand implements Command
{
    private $travelSlug;
    private $user;

    public function __construct(string $travelSlug, User $user)
    {
        $this->travelSlug = $travelSlug;
        $this->user = $user;
    }

    public function getTravelSlug(): string
    {
        return $this->travelSlug;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
