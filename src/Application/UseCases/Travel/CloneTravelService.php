<?php

namespace App\Application\UseCases\Travel;

use App\Application\Command\Travel\CloneTravelCommand;
use App\Application\UseCases\UsesCasesService;
use App\Domain\Event\DomainEventPublisher;
use App\Domain\Location\Model\Location;
use App\Domain\Travel\Events\TravelWasCloned;
use App\Domain\Travel\Model\Travel;
use App\Domain\Travel\Repository\TravelRepository;
use App\Domain\TravelClone\Model\TravelClone;
use App\Domain\TravelClone\Repository\TravelCloneRepository;
use App\Domain\User\Model\User;
use App\Domain\User\Repository\UserRepository;

class CloneTravelService implements UsesCasesService
{
    /** @var TravelRepository */
    private $travelRepository;
    /** @var UserRepository */
    private $userRepository;
    /** @var TravelCloneRepository */
    private $travelCloneRepository;

    public function __construct(
        TravelRepository $travelRepository,
        UserRepository $userRepository,
        TravelCloneRepository $travelCloneRepository
    ) {
        $this->travelRepository = $travelRepository;
        $this->userRepository = $userRepository;
        $this->travelCloneRepository = $travelCloneRepository;
    }

    public function __invoke(CloneTravelCommand $command): Travel
    {
        $travelSlug = $command->getTravelSlug();
        $user = $command->getUser();

        $this->userRepository->ofIdOrFail($user->getId());
        $originalTravel = $this->travelRepository->ofSlugOrFail($travelSlug);

        if (!$originalTravel->isPublished()) {
            throw new \InvalidArgumentException('Cannot clone a travel that is not published');
        }

        $clonedTravel = $this->deepCloneTravel($originalTravel, $user);

        $this->travelRepository->save($clonedTravel);

        $travelClone = new TravelClone(
            $originalTravel->getId()->id(),
            $clonedTravel->getId()->id(),
            $user->getId()->id(),
            $originalTravel->getUser()->getId()->id(),
            $originalTravel->getTitle()
        );

        $this->travelCloneRepository->save($travelClone);

        DomainEventPublisher::instance()->publish(
            new TravelWasCloned(
                $originalTravel->toArray(),
                $clonedTravel->toArray(),
                $user->getId()->id()
            )
        );

        return $clonedTravel;
    }

    private function deepCloneTravel(Travel $original, User $newUser): Travel
    {
        $clone = new Travel();
        $clone->setTitle($original->getTitle());
        $clone->setDescription($original->getDescription());
        $clone->setGeoLocation($original->getGeoLocation());
        if ($original->getStartAt()) {
            $clone->setStartAt(clone $original->getStartAt());
        }
        if ($original->getEndAt()) {
            $clone->setEndAt(clone $original->getEndAt());
        }
        if ($original->getPhoto()) {
            $clone->setPhoto($original->getPhoto());
        }
        $clone->setStars(0);
        $clone->setWatch(0);
        $clone->setStatus(Travel::TRAVEL_DRAFT);
        $clone->setUser($newUser);

        $slug = $original->getSlug() . '-copy-' . uniqid();
        $clone->setSlug($slug);

        foreach ($original->getLocation() as $originalLocation) {
            $clonedLocation = $this->deepCloneLocation($originalLocation, $clone);
            $clone->getLocation()->add($clonedLocation);
        }

        return $clone;
    }

    private function deepCloneLocation(Location $original, Travel $newTravel): Location
    {
        $clone = new Location();
        $clone->setTitle($original->getTitle());
        $clone->setUrl($original->getUrl());
        $clone->setSlug($original->getSlug());
        $clone->setDescription($original->getDescription());
        $clone->setStars($original->getStars());
        $clone->setVisitAt($original->getVisitAt());
        $clone->setTravel($newTravel);
        $clone->setMark($original->getMark());
        try {
            $clone->setTypeLocation($original->getTypeLocation());
        } catch (\TypeError $e) {
        }

        foreach ($original->getVisitDates() as $vd) {
            $newVd = $clone->addVisitDate(clone $vd->getVisitDate());
            $newVd->setPosition($vd->getPosition());
            if ($vd->getTimeStart()) {
                $newVd->setTimeStart(clone $vd->getTimeStart());
            }
            if ($vd->getTimeEnd()) {
                $newVd->setTimeEnd(clone $vd->getTimeEnd());
            }
        }

        foreach ($original->getNotas() as $note) {
            $newNote = new \App\Domain\Note\Model\Note();
            $newNote->setTitle($note->getTitle());
            $newNote->setDescription($note->getDescription());
            $newNote->setLocation($clone);
            $clone->getNotas()->add($newNote);
        }

        return $clone;
    }
}
