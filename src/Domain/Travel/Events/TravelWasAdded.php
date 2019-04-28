<?php

namespace App\Domain\Travel\Events;

use App\Domain\Event\DomainEvent;
use App\Domain\Travel\Model\Travel;

class TravelWasAdded implements DomainEvent
{
    const ADD_TRAVEL_EVENT_REQUEST = 'add_travel_request_event';

    /** @var array */
    private $travel;

    /** @var \DateTime */
    private $occuredOn;

    public function __construct(array $travel)
    {
        $this->travel = $travel;
        $this->occuredOn = new \DateTime();
    }

    /**
     * @return Travel
     */
    public function getTravel(): array
    {
        return $this->travel;
    }


    /**
     * @param \DateTime $occuredOn
     */
    public function setOccuredOn(\DateTime $occuredOn): void
    {
        $this->occuredOn = $occuredOn;
    }

    /**
     * @return \DateTime
     */
    public function occurredOn()
    {
        return $this->occuredOn;
    }
}
