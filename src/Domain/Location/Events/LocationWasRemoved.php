<?php

namespace App\Domain\Location\Events;

use App\Domain\Event\DomainEvent;

class LocationWasRemoved implements DomainEvent
{
    const REMOVE_LOCATION_EVENT_REQUEST = 'remove_location_request_event';

    private $locationId;
    private $userId;
    private $travelId;

    private $occurredOn;

    public function __construct($locationId, $userId, $travelId)
    {
        $this->locationId = $locationId;
        $this->userId = $userId;
        $this->travelId = $travelId;
        $this->occurredOn = new \DateTime();
    }

    public function occurredOn(): \DateTime
    {
        return $this->occurredOn;
    }
}
