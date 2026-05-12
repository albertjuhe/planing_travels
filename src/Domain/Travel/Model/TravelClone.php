<?php

namespace App\Domain\Travel\Model;

use App\Domain\Travel\ValueObject\TravelId;
use App\Domain\User\Model\User;
use App\Domain\User\ValueObject\UserId;
use Ramsey\Uuid\Uuid;

class TravelClone
{
    /** @var string */
    private $id;

    /** @var string */
    private $sourceTravelId;

    /** @var int */
    private $sourceUserId;

    /** @var string */
    private $sourceTitleSnapshot;

    /** @var Travel */
    private $targetTravel;

    /** @var User */
    private $clonedByUser;

    /** @var \DateTime */
    private $clonedAt;

    /** @var int */
    private $depth;

    public function __construct(
        string $sourceTravelId,
        int $sourceUserId,
        string $sourceTitleSnapshot,
        Travel $targetTravel,
        User $clonedByUser,
        int $depth = 1
    ) {
        $this->id = Uuid::uuid4()->toString();
        $this->sourceTravelId = $sourceTravelId;
        $this->sourceUserId = $sourceUserId;
        $this->sourceTitleSnapshot = $sourceTitleSnapshot;
        $this->targetTravel = $targetTravel;
        $this->clonedByUser = $clonedByUser;
        $this->clonedAt = new \DateTime();
        $this->depth = $depth;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getSourceTravelId(): string
    {
        return $this->sourceTravelId;
    }

    public function getSourceUserId(): int
    {
        return $this->sourceUserId;
    }

    public function getSourceTitleSnapshot(): string
    {
        return $this->sourceTitleSnapshot;
    }

    public function getTargetTravel(): Travel
    {
        return $this->targetTravel;
    }

    public function getClonedByUser(): User
    {
        return $this->clonedByUser;
    }

    public function getClonedAt(): \DateTime
    {
        return $this->clonedAt;
    }

    public function getDepth(): int
    {
        return $this->depth;
    }
}
