<?php

namespace App\Application\Command\Travel;

use App\Application\Command\Command;

class ShareTravelCommand implements Command
{
    /** @var string */
    private $travelId;

    /** @var int */
    private $ownerUserId;

    /** @var string */
    private $targetUsername;

    public function __construct(string $travelId, int $ownerUserId, string $targetUsername)
    {
        $this->travelId = $travelId;
        $this->ownerUserId = $ownerUserId;
        $this->targetUsername = $targetUsername;
    }

    public function getTravelId(): string
    {
        return $this->travelId;
    }

    public function getOwnerUserId(): int
    {
        return $this->ownerUserId;
    }

    public function getTargetUsername(): string
    {
        return $this->targetUsername;
    }
}
