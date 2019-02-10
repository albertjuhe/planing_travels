<?php


namespace App\Domain\Travel\Events;

use App\Domain\Event\DomainEvent;
use App\Domain\Travel\Model\Travel;

class TravelWasAdded implements DomainEvent
{
    const ADD_TRAVEL_EVENT_REQUEST = 'add_travel_request_event';

    /** @var Travel */
    private $travel;
    /** @var \DateTime */
    private $occuredOn;

    /**
     * travelWasAdded constructor.
     * @param Travel $travel
     * @throws \Exception
     */
    public function __construct(Travel $travel)
    {
        $this->travel = $travel;
        $this->occuredOn = new \DateTime();
    }

    /**
     * @return Travel
     */
    public function getTravel(): Travel
    {
        return $this->travel;
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

    /**
     * @return \DateTime
     */
    public function occurredOn()
    {
        return $this->occuredOn;
    }

}