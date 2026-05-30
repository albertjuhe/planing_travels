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
use App\Infrastructure\WebSocket\WebSocketNotifier;

class DeleteLocationService implements UsesCasesService
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly LocationRepository $locationRepository,
        private readonly WebSocketNotifier $webSocketNotifier,
    ) {
    }

    public function __invoke(DeleteLocationCommand $deleteLocationCommand)
    {
        $locationId = $deleteLocationCommand->getLocationId();
        $userId = $deleteLocationCommand->getUserId();
        $travelId = $deleteLocationCommand->getTravelId();

        $location = $this->locationRepository->findById($locationId);
        $user = $this->userRepository->ofIdOrFail($userId);

        if (!$location instanceof Location) {
            throw new LocationDoesntExists();
        }

        DomainEventPublisher::instance()->publish(new LocationWasRemoved($locationId, $travelId, $userId));

        $this->locationRepository->remove($location);

        $this->webSocketNotifier->notifyLocationRemoved(
            $travelId,
            $locationId,
            (string) $userId,
            $user->getUsername()
        );
    }
}
