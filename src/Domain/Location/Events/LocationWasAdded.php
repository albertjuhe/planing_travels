<?php

namespace App\Domain\Location\Events;

use App\Domain\Event\DomainEvent;

class LocationWasAdded implements DomainEvent
{
    const ADD_LOCATION_EVENT_REQUEST = 'add_location_request_event';

    /** @var array */
    private $location;
    /** @var \DateTime */
    private $occuredOn;

    public function __construct(array $location)
    {
        $this->location = $location;
        $this->occuredOn = new \DateTime();
    }

    public function getLocation(): array
    {
        return $this->location;
    }

    /**
     * @return \DateTime
     */
    public function occurredOn()
    {
        return $this->occuredOn;
    }
}
