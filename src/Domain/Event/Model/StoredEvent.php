<?php

namespace App\Domain\Event\Model;

use App\Domain\Event\DomainEvent;

class StoredEvent implements DomainEvent
{
    private $eventId;
    private $eventBody;
    private $occurredOn;
    private $typeName;

    /**
     * @param string    $aTypeName
     * @param \DateTime $anOccurredOn
     * @param string    $anEventBody
     */
    public function __construct(
        $aTypeName,
        \DateTime $anOccurredOn,
        $anEventBody
    ) {
        $this->eventBody = $anEventBody;
        $this->typeName = $aTypeName;
        $this->occurredOn = $anOccurredOn;
    }

    public function eventBody()
    {
        return $this->eventBody;
    }

    public function eventId()
    {
        return $this->eventId;
    }

    public function typeName()
    {
        return $this->typeName;
    }

    public function occurredOn()
    {
        return $this->occurredOn;
    }
}
