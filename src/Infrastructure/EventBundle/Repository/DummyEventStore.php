<?php
declare(strict_types=1);

namespace App\Infrastructure\EventBundle\Repository;

use App\Domain\Event\DomainEvent;
use App\Domain\Event\Model\StoredEvent;
use App\Domain\Event\Repository\EventStore;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class DummyEventStore extends ServiceEntityRepository implements EventStore
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StoredEvent::class);
    }

    public function append(DomainEvent $aDomainEvent)
    {
        // TODO: Implement append() method.
    }

    public function allStoredEventsSince($anEventId)
    {
        // TODO: Implement allStoredEventsSince() method.
    }

}