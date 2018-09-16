<?php


namespace App\Infrastructure\EventBundle\Repository;


use App\Domain\Common\Event\DomainEvent;
use App\Domain\Common\Event\EventStore;

class DoctrineEventStore extends ServiceEntityRepository implements EventStore
{
    public function append(DomainEvent $aDomainEvent)
    {
        // TODO: Implement append() method.
    }

    public function allStoredEventsSince($anEventId)
    {
        // TODO: Implement allStoredEventsSince() method.
    }

}