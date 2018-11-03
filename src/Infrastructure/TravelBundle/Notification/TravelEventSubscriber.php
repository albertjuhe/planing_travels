<?php
/**
 * Created by PhpStorm.
 * User: albert.juhe
 * Date: 24/10/2018
 * Time: 18:53
 */

namespace App\Infrastructure\TravelBundle\Notification;

use App\Domain\Event\DomainEvent;
use App\Domain\Event\DomainEventSubscriber;
use App\Domain\Travel\Events\TravelWasAdded;
use App\Domain\Travel\Events\TravelWasPublished;

class TravelEventSubscriber implements DomainEventSubscriber
{
    public function handle(DomainEvent $domainEvent)
    {
        // TODO: Implement handle() method.
    }

    /**
     * Check the domainEvent to treat
     * @param DomainEvent $domainEvent
     * @return bool|mixed
     */
    public function isSubscribedTo(DomainEvent $domainEvent)
    {
       return ($domainEvent instanceof TravelWasPublished ||
           $domainEvent instanceof TravelWasAdded);
    }
}