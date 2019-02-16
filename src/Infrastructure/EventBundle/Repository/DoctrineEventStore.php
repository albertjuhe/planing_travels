<?php

namespace App\Infrastructure\EventBundle\Repository;

use App\Domain\Event\DomainEvent;
use App\Domain\Event\Model\StoredEvent;
use App\Domain\Event\Repository\EventStore;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use JMS\Serializer\SerializerInterface;

class DoctrineEventStore extends ServiceEntityRepository implements EventStore
{
    const SERIALIZE_JSON = 'json';
    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * DoctrineEventStore constructor.
     *
     * @param ManagerRegistry $registry
     * @param Serializer      $serializer
     */
    public function __construct(ManagerRegistry $registry, SerializerInterface $serializer)
    {
        parent::__construct($registry, StoredEvent::class);
        $this->serializer = $serializer;
    }

    public function append(DomainEvent $aDomainEvent)
    {
        $storedEvent = new StoredEvent(
            get_class($aDomainEvent),
            $aDomainEvent->occurredOn(),
            $this->serializer->serialize($aDomainEvent, self::SERIALIZE_JSON)
        );
        $this->save($storedEvent);
    }

    public function allStoredEventsSince($anEventId)
    {
        $query = $this->createQueryBuilder('e');
        if ($anEventId) {
            $query->where('e.id > :eventId')
               ->setParameter('eventId', $anEventId);
        }
        $query->orderBy('e.id');
    }

    public function save(StoredEvent $storedEvent)
    {
        $this->_em->persist($storedEvent);
    }
}
