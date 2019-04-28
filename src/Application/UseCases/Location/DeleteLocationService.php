<?php

namespace App\Application\UseCases\Location;

use App\Application\Command\Location\DeleteLocationCommand;
use App\Application\UseCases\UsesCasesService;
use App\Domain\Event\DomainEventPublisher;
use App\Domain\Location\Events\LocationWasRemoved;
use App\Domain\Location\Exceptions\LocationDoesntExists;
use App\Domain\Location\Model\Location;
use App\Domain\Location\Repository\LocationRepository;
use App\Domain\User\Repository\UserRepository;
use App\Domain\User\ValueObject\UserId;

class DeleteLocationService implements UsesCasesService
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var LocationRepository
     */
    private $locationRepository;

    /**
     * DeleteLocationService constructor.
     *
     * @param UserRepository     $userRepository
     * @param LocationRepository $locationRepository
     */
    public function __construct(
        UserRepository $userRepository,
        LocationRepository $locationRepository
    ) {
        $this->userRepository = $userRepository;
        $this->locationRepository = $locationRepository;
    }

    public function handle(DeleteLocationCommand $deleteLocationCommand)
    {
        $locationId = $deleteLocationCommand->getLocationId();
        $location = $this->locationRepository->findById($locationId);

        /** @var UserId $userId */
        $userId = $deleteLocationCommand->getUserId();
        $this->userRepository->ofIdOrFail($userId);

        $travelId = $deleteLocationCommand->getTravelId();

        if (!$location instanceof Location) {
            throw new LocationDoesntExists();
        }

        DomainEventPublisher::instance()->publish(new LocationWasRemoved($locationId, $travelId, $userId));

        $this->locationRepository->remove($location);
    }
}
