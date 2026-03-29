<?php

namespace App\Infrastructure\TravelBundle\Notification;

use App\Domain\Event\DomainEvent;
use App\Domain\Event\DomainEventSubscriber;
use App\Domain\Travel\Events\TravelWasAdded;
use App\Domain\Travel\Events\TravelWasPublished;
use App\Domain\Travel\Repository\IndexerRepository;
use App\Domain\Travel\Repository\TravelRepository;

class TravelEventSubscriber implements DomainEventSubscriber
{
    private $travelRepository;
    private $indexerRepository;

    public function __construct(TravelRepository $travelRepository, IndexerRepository $indexerRepository)
    {
        $this->travelRepository  = $travelRepository;
        $this->indexerRepository = $indexerRepository;
    }

    public function handle(DomainEvent $domainEvent)
    {
        if ($domainEvent instanceof TravelWasPublished) {
            $travelData = $domainEvent->getTravel();
            $travel = $this->travelRepository->ofIdOrFail($travelData['id']);
            $this->indexerRepository->save($travel);
        }
    }

    public function isSubscribedTo(DomainEvent $domainEvent)
    {
        return $domainEvent instanceof TravelWasPublished ||
           $domainEvent instanceof TravelWasAdded;
    }
}
