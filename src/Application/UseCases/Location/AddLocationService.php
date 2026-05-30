<?php

namespace App\Application\UseCases\Location;

use App\Application\Command\Location\AddLocationCommand;
use App\Application\UseCases\UsesCasesService;
use App\Domain\Location\Model\Location;
use App\Domain\Location\Repository\LocationRepository;
use App\Domain\Mark\Model\Mark;
use App\Domain\Mark\Repository\MarkRepository;
use App\Domain\Travel\Exceptions\InvalidTravelUser;
use App\Domain\Travel\Model\Travel;
use App\Domain\Travel\Repository\TravelRepository;
use App\Domain\Travel\ValueObject\GeoLocation;
use App\Domain\TypeLocation\Repository\TypeLocationRepository;
use App\Domain\User\Repository\UserRepository;
use App\Domain\User\ValueObject\UserId;
use App\Infrastructure\WebSocket\WebSocketNotifier;
use Symfony\Component\String\Slugger\AsciiSlugger;

class AddLocationService implements UsesCasesService
{
    public function __construct(
        private readonly TravelRepository $travelRepository,
        private readonly UserRepository $userRepository,
        private readonly MarkRepository $markRepository,
        private readonly LocationRepository $locationRepository,
        private readonly TypeLocationRepository $typeLocationRepository,
        private readonly WebSocketNotifier $webSocketNotifier,
    ) {
    }

    public function __invoke(AddLocationCommand $command): string
    {
        $user = $this->userRepository->ofIdOrFail(new UserId($command->getUserId()));
        if (is_null($user)) {
            throw new InvalidTravelUser('User does not exists');
        }

        $travel = $this->travelRepository->ofIdOrFail($command->getTravelId());

        $isOwner = $travel->getUser()->getId()->equalsTo($user->getId());
        $isSharedUser = $travel->getSharedusers()->exists(
            function ($key, $sharedUser) use ($user) {
                return $sharedUser->getId()->equalsTo($user->getId());
            }
        );

        if (!$isOwner && !$isSharedUser) {
            throw new InvalidTravelUser('This user is not allowed to modify the travel');
        }

        $locationType = $this->typeLocationRepository->idOrFail($command->getLocationType());

        $location = Location::fromTitleAndUrlAndDescription(
            $command->getTitle(),
            $command->getLink(),
            $command->getDescription(),
        );

        $location = $this->setSlug($location);

        $geolocation = new GeoLocation($command->getLatitude(), $command->getLongitude(), 0, 0, 0, 0);
        $mark = Mark::fromGeolocationAndId($geolocation, $command->getPlaceId());
        $mark->setTitle($command->getAddress());
        $mark = $this->markRepository->ofIdOrSave($mark);

        $location->setTravel($travel);
        $location->setMark($mark);
        $location->setTypeLocation($locationType);

        $this->locationRepository->save($location);

        $locationId = $location->getId()->id();
        $command->setLocationId($locationId);

        $this->webSocketNotifier->notifyLocationAdded(
            $command->getTravelId(),
            [
                'id'            => $location->getId()->id(),
                'title'         => $location->getTitle(),
                'latitude'      => $location->getMark()->getGeoLocation()->lat(),
                'longitude'     => $location->getMark()->getGeoLocation()->lng(),
                'slug'          => $location->getSlug(),
                'addedByUserId' => $command->getUserId(),
                'addedByUsername' => $user->getUsername(),
            ]
        );

        return $locationId;
    }

    private function setSlug(Location $location): Location
    {
        if (!$location->getSlug()) {
            $slugger = new AsciiSlugger();
            $slug = strtolower((string) $slugger->slug($location->getTitle()));
            $location->setSlug($slug ?: 'location-' . uniqid());
        }

        return $location;
    }
}
