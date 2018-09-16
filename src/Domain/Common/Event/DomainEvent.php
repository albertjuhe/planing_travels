<?php


namespace App\Domain\Common\Event;


interface DomainEvent
{
    /**
     * @return \DateTime
     */
    public function occurredOn();
}