<?php


namespace App\Domain\Common\Event;

interface EventStore
{
    public function append(DomainEvent $aDomainEvent);
    public function allStoredEventsSince($anEventId);
}