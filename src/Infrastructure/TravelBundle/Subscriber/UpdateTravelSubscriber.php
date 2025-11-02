<?php

declare(strict_types=1);

namespace App\Infrastructure\TravelBundle\Subscriber;

use App\Domain\Travel\Events\TravelWasUpdated;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UpdateTravelSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            TravelWasUpdated::class => ['onTravelWasUpdated']
        ];
    }

    public function onTravelWasUpdated(TravelWasUpdated $event): void
    {
        $travel = $event->getTravel();
    }

}
