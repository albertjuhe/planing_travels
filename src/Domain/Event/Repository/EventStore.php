<?php


namespace App\Domain\Event\Repository;

use App\Domain\Common\Model\DomainEvent;

interface EventStore
{
    public function append(DomainEvent $aDomainEvent);
    public function allStoredEventsSince($anEventId);
}