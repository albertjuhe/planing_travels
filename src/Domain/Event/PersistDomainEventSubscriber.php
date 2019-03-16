<?php

namespace App\Domain\Event;

use App\Domain\Common\Model\TriggerEventsTrait;
use App\Domain\Event\Repository\EventStore;

/**
 * Its very important to store all events, is the best way to track or recover events
 * Class PersistDomainEventSubscriber.
 */
class PersistDomainEventSubscriber implements DomainEventSubscriber
{
    use TriggerEventsTrait;

    private $eventStore;

    /**
     * PersistDomainEventSubscriber constructor.
     * PersistDomainEventSubscriber constructor.
     *
     * @param EventStore $eventStore
     */
    public function __construct(EventStore $eventStore)
    {
        $this->eventStore = $eventStore;
    }

    /**
     * Persisting the domain.
     *
     * @param DomainEvent $domainEvent
     *
     * @return mixed|void
     */
    public function handle(DomainEvent $domainEvent)
    {
        $this->trigger($domainEvent);
        $this->eventStore->append($domainEvent);
    }

    /**
     * Is subscribed to all events.
     *
     * @param DomainEvent $domainEvent
     *
     * @return bool|mixed
     */
    public function isSubscribedTo(DomainEvent $domainEvent)
    {
        return true;
    }
}
