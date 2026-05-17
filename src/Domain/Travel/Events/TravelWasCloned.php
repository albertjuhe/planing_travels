<?php

namespace App\Domain\Travel\Events;

use App\Domain\Event\DomainEvent;

class TravelWasCloned implements DomainEvent
{
    public const CLONE_TRAVEL_EVENT_REQUEST = 'clone_travel_request_event';

    /** @var array */
    private $originalTravel;

    /** @var array */
    private $clonedTravel;

    /** @var int */
    private $clonedByUserId;

    /** @var \DateTime */
    private $occuredOn;

    public function __construct(array $originalTravel, array $clonedTravel, int $clonedByUserId)
    {
        $this->originalTravel = $originalTravel;
        $this->clonedTravel = $clonedTravel;
        $this->clonedByUserId = $clonedByUserId;
        $this->occuredOn = new \DateTime();
    }

    public function getOriginalTravel(): array
    {
        return $this->originalTravel;
    }

    public function getClonedTravel(): array
    {
        return $this->clonedTravel;
    }

    public function getClonedByUserId(): int
    {
        return $this->clonedByUserId;
    }

    public function occurredOn()
    {
        return $this->occuredOn;
    }

    public function setOccuredOn(\DateTime $occuredOn): void
    {
        $this->occuredOn = $occuredOn;
    }
}
