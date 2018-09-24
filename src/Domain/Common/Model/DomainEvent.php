<?php


namespace App\Domain\Common\Model;


interface DomainEvent
{
    /**
     * @return \DateTime
     */
    public function occurredOn();
}