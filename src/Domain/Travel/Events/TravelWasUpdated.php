<?php

namespace App\Domain\Travel\Events;

use App\Domain\Event\DomainEvent;
use App\Domain\Travel\Model\Travel;

class TravelWasUpdated implements DomainEvent
{
    public const ADD_TRAVEL_EVENT_REQUEST = 'add_travel_request_event';

    /** @var Travel */
    private $travel;
    /** @var \DateTime */
    private $occuredOn;

    /**
     * travelWasAdded constructor.
     *
     * @param array $travel
     */
    public function __construct(array $travel)
    {
        $this->travel = $travel;
        $this->occuredOn = new \DateTime();
    }

    /**
     * @return \DateTime
     */
    public function occurredOn()
    {
        return $this->occuredOn;
    }

    /**
     * @return Travel
     */
    public function getTravel(): array
    {
        return $this->travel;
    }

    /**
    /**
     * @param Travel $travel
     */
    public function setTravel(array $travel): void
    {
        $this->travel = $travel;
    }

    /**
     * @return \DateTime
     */
    public function getOccuredOn(): \DateTime
    {
        return $this->occuredOn;
    }

    /**
     * @param \DateTime $occuredOn
     */
    public function setOccuredOn(\DateTime $occuredOn): void
    {
        $this->occuredOn = $occuredOn;
    }
}
