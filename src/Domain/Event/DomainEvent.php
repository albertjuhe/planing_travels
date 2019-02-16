<?php

namespace App\Domain\Event;

interface DomainEvent
{
    /**
     * @return \DateTime
     */
    public function occurredOn();
}
