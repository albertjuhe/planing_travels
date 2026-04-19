<?php

namespace App\Application\UseCases\Location;

use App\Application\Command\Location\AddLocationCommand;
use App\Application\UseCases\UsesCasesService;
use App\Domain\Location\Repository\LocationRepository;
use App\Domain\Mark\Repository\MarkRepository;
use App\Domain\Travel\Exceptions\InvalidTravelUser;
use App\Domain\Travel\Repository\TravelRepository;
use App\Domain\TypeLocation\Repository\TypeLocationRepository;
use App\Domain\User\Repository\UserRepository;
use App\Domain\User\ValueObject\UserId;
use App\Infrastructure\WebSocket\WebSocketNotifier;

class AddLocationService implements UsesCasesService
{
    /**
     * @var TravelRepository;
     */
    private $travelRepository;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var MarkRepository
     */
    private $markRepository;
    /**
     * @var LocationRepository
     */
    private $locationRepository;

    /**
     * @var TypeLocationRepository
     */
    private $typeLocationRepository;

    /**
     * @var WebSocketNotifier
     */
    private $webSocketNotifier;

    public function __construct(
        TravelRepository $travelRepository,
        UserRepository $userRepository,
        MarkRepository $markRepository,
        LocationRepository $locationRepository,
        TypeLocationRepository $typeLocationRepository,
        WebSocketNotifier $webSocketNotifier
    ) {
        $this->travelRepository = $travelRepository;
        $this->userRepository = $userRepository;
        $this->markRepository = $markRepository;
        $this->locationRepository = $locationRepository;
        $this->typeLocationRepository = $typeLocationRepository;
        $this->webSocketNotifier = $webSocketNotifier;
    }

    /**
     * @throws InvalidTravelUser
     */
    public function handle(AddLocationCommand $addLocationCommand): void
    {
        $travelId = $addLocationCommand->getTravelId();
        $location = $addLocationCommand->getLocation();
        $userId = $addLocationCommand->getUser();
        $mark = $addLocationCommand->getMark();
        $locationType = $addLocationCommand->getLocationType();

        $user = $this->userRepository->ofIdOrFail(new UserId($userId));

        if (is_null($user)) {
            throw new InvalidTravelUser('User does not exists');
        }

        $travel = $this->travelRepository->ofIdOrFail($travelId);

        $isOwner = $travel->getUser()->getId()->equalsTo($user->getId());
        $isSharedUser = $travel->getSharedusers()->exists(
            function ($key, $sharedUser) use ($user) {
                return $sharedUser->getId()->equalsTo($user->getId());
            }
        );

        if (!$isOwner && !$isSharedUser) {
            throw new InvalidTravelUser('This user is not allowed to modify the travel');
        }
        $locationType = $this->typeLocationRepository->idOrFail($locationType);

        //find the mark if not exists create it
        $mark = $this->markRepository->ofIdOrSave($mark);

        $location->setTravel($travel);
        $location->setMark($mark);
        $location->setTypeLocation($locationType);

        $this->locationRepository->save($location);

        $this->webSocketNotifier->notifyLocationAdded(
            $travelId,
            [
                'id'            => $location->getId()->id(),
                'title'         => $location->getTitle(),
                'latitude'      => $location->getMark()->getGeoLocation()->lat(),
                'longitude'     => $location->getMark()->getGeoLocation()->lng(),
                'slug'          => $location->getSlug(),
                'addedByUserId' => $userId,
                'addedByUsername' => $user->getUsername(),
            ]
        );
    }
}
