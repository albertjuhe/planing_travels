<?php

namespace App\Domain\Travel\Events;

use App\Domain\Event\DomainEvent;

class TravelWasCloned implements DomainEvent
{
    /** @var string */
    private $targetTravelId;

    /** @var string */
    private $sourceTravelId;

    /** @var int */
    private $clonedByUserId;

    /** @var int */
    private $sourceUserId;

    /** @var string */
    private $sourceTitleSnapshot;

    /** @var \DateTime */
    private $occurredOn;

    public function __construct(
        string $targetTravelId,
        string $sourceTravelId,
        int $clonedByUserId,
        int $sourceUserId,
        string $sourceTitleSnapshot
    ) {
        $this->targetTravelId = $targetTravelId;
        $this->sourceTravelId = $sourceTravelId;
        $this->clonedByUserId = $clonedByUserId;
        $this->sourceUserId = $sourceUserId;
        $this->sourceTitleSnapshot = $sourceTitleSnapshot;
        $this->occurredOn = new \DateTime();
    }

    public function getTargetTravelId(): string
    {
        return $this->targetTravelId;
    }

    public function getSourceTravelId(): string
    {
        return $this->sourceTravelId;
    }

    public function getClonedByUserId(): int
    {
        return $this->clonedByUserId;
    }

    public function getSourceUserId(): int
    {
        return $this->sourceUserId;
    }

    public function getSourceTitleSnapshot(): string
    {
        return $this->sourceTitleSnapshot;
    }

    public function occurredOn(): \DateTime
    {
        return $this->occurredOn;
    }
}
