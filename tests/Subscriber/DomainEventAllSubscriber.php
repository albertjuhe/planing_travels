<?php

namespace App\Tests\Subscriber;

use App\Domain\Common\Model\TriggerEventsTrait;
use App\Domain\Event\DomainEvent;
use App\Domain\Event\DomainEventSubscriber;

/**
 * This subscriber is subscribed to all events
 * Class GeneralEventSubscriber.
 */
class DomainEventAllSubscriber implements DomainEventSubscriber
{
    use TriggerEventsTrait;

    public function handle(DomainEvent $domainEvent)
    {
        $this->trigger($domainEvent);
    }

    public function isSubscribedTo(DomainEvent $domainEvent)
    {
        return true;
    }
}
