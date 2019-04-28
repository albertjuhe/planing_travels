<?php

namespace App\Application\Command\Location;

use App\Application\Command\Command;
use App\Domain\User\ValueObject\UserId;

class DeleteLocationCommand implements Command
{
    private $locationId;
    private $userId;
    private $travelId;

    public function __construct(string $locationId, string $travelId, UserId $userId)
    {
        $this->locationId = $locationId;
        $this->travelId = $travelId;
        $this->userId = $userId;
    }

    /**
     * @return string
     */
    public function getLocationId(): string
    {
        return $this->locationId;
    }

    /**
     * @return UserId
     */
    public function getUserId(): UserId
    {
        return $this->userId;
    }

    /**
     * @return string
     */
    public function getTravelId(): string
    {
        return $this->travelId;
    }
}
