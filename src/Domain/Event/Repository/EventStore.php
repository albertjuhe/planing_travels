<?php


namespace App\Domain\Event\Repository;

interface EventStore
{
    public function append(DomainEvent $aDomainEvent);
    public function allStoredEventsSince($anEventId);
}