<?php

namespace App\Domain\Travel\Events;

use App\Domain\Event\DomainEvent;

class TravelWasPublished implements DomainEvent
{
    const PUBLISH_TRAVEL_EVENT_REQUEST = 'publish_travel_request_event';

    /** @var array */
    private $travel;
    /** @var int */
    private $user;
    /** @var \DateTime */
    private $occuredOn;

    /**
     * travelWasAdded constructor.
     *
     * @param array $travel
     * @param int   $user
     */
    public function __construct(array $travel, int $user)
    {
        $this->travel = $travel;
        $this->user = $user;
        $this->occuredOn = new \DateTime();
    }

    /**
     * @return array
     */
    public function getTravel(): array
    {
        return $this->travel;
    }

    /**
     * @param array $travel
     */
    public function setTravel(array $travel): void
    {
        $this->travel = $travel;
    }

    /**
     * @return int
     */
    public function getUser(): int
    {
        return $this->user;
    }

    /**
     * @param int $user
     */
    public function setUser(int $user): void
    {
        $this->user = $user;
    }

    /**
     * @return \DateTime
     */
    public function occurredOn()
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
