<?php


namespace App\Domain\Event\Repository;

use App\Domain\Event\DomainEvent;

/**
 * Is the Domain Event repository
 * Interface EventStore
 * @package App\Domain\Event\Repository
 */
interface EventStore
{
    public function append(DomainEvent $aDomainEvent);
    public function allStoredEventsSince($anEventId);
}