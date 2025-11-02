<?php

namespace App\Domain\Event;

interface DomainEventSubscriber
{
    /**
     * Execute the subscriber with the domain event.
     *
     * @param DomainEvent $domainEvent
     *
     * @return mixed
     */
    public function handle(DomainEvent $domainEvent);

    /**
     * Check if this DomainEvent should be treated.
     *
     * @param DomainEvent $domainEvent
     *
     * @return mixed
     */
    public function isSubscribedTo(DomainEvent $domainEvent);
}
