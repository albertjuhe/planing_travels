<?php

namespace App\Domain\Event\Repository;

use App\Domain\Event\DomainEvent;

/**
 * Is the Domain Event repository
 * Interface EventStore.
 */
interface EventStore
{
    public function append(DomainEvent $aDomainEvent);

    public function allStoredEventsSince($anEventId);
}
