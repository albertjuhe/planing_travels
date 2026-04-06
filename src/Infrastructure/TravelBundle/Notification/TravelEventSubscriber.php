<?php

namespace App\Infrastructure\TravelBundle\Notification;

use App\Domain\Event\DomainEvent;
use App\Domain\Event\DomainEventSubscriber;
use App\Domain\Travel\Events\TravelWasAdded;
use App\Domain\Travel\Events\TravelWasPublished;
use App\Domain\Travel\Repository\TravelRepository;

class TravelEventSubscriber implements DomainEventSubscriber
{
    private $travelRepository;

    public function __construct(TravelRepository $travelRepository)
    {
        $this->travelRepository = $travelRepository;
    }

    public function handle(DomainEvent $domainEvent)
    {
        if ($domainEvent instanceof TravelWasPublished) {
            $travelData = $domainEvent->getTravel();
            $this->travelRepository->ofIdOrFail($travelData['id']);
        }
    }

    public function isSubscribedTo(DomainEvent $domainEvent)
    {
        return $domainEvent instanceof TravelWasPublished ||
           $domainEvent instanceof TravelWasAdded;
    }
}
