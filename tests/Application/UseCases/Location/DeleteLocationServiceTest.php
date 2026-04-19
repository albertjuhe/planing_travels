<?php

namespace App\Tests\Application\UseCases\Location;

use App\Application\Command\Location\DeleteLocationCommand;
use App\Application\UseCases\Location\DeleteLocationService;
use App\Domain\Location\Exceptions\LocationDoesntExists;
use App\Domain\Location\Model\Location;
use App\Domain\User\ValueObject\UserId;

class DeleteLocationServiceTest extends LocationService
{
    public function setUp()
    {
        parent::setUp();
    }

    public function testDeleteLocation(): void
    {
        $locationId = (string) mt_rand();
        $travelId = (string) mt_rand();
        $userId = new UserId(mt_rand());

        $deleteLocationCommand = new DeleteLocationCommand($locationId, $travelId, $userId);

        $location = $this->createMock(Location::class);

        $this->locationRepository
            ->expects($this->once())
            ->method('findById')
            ->with($locationId)
            ->willReturn($location);

        $this->userRepository
            ->expects($this->once())
            ->method('ofIdOrFail')
            ->with($userId)
            ->willReturn($this->createConfiguredMock(\App\Domain\User\Model\User::class, ['getUsername' => 'testuser']));

        $this->locationRepository
            ->expects($this->once())
            ->method('remove')
            ->with($location);

        $this->webSocketNotifier
            ->expects($this->once())
            ->method('notifyLocationRemoved')
            ->with($travelId, $locationId, (string) $userId, 'testuser');

        $deleteLocationService = new DeleteLocationService(
            $this->userRepository,
            $this->locationRepository,
            $this->webSocketNotifier
        );

        $deleteLocationService->handle($deleteLocationCommand);
    }

    public function testDeleteLocationNotifiesWithCorrectTravelId(): void
    {
        $locationId = (string) mt_rand();
        $travelId = 'travel-uuid-123';
        $userId = new UserId(mt_rand());

        $deleteLocationCommand = new DeleteLocationCommand($locationId, $travelId, $userId);

        $location = $this->createMock(Location::class);

        $this->locationRepository->method('findById')->willReturn($location);
        $this->userRepository->method('ofIdOrFail')
            ->willReturn($this->createConfiguredMock(\App\Domain\User\Model\User::class, ['getUsername' => 'testuser']));

        $this->webSocketNotifier
            ->expects($this->once())
            ->method('notifyLocationRemoved')
            ->with($travelId, $locationId, (string) $userId, 'testuser');

        $deleteLocationService = new DeleteLocationService(
            $this->userRepository,
            $this->locationRepository,
            $this->webSocketNotifier
        );

        $deleteLocationService->handle($deleteLocationCommand);
    }

    public function testDeleteNonExistentLocationThrows(): void
    {
        $this->expectException(LocationDoesntExists::class);

        $locationId = (string) mt_rand();
        $travelId = (string) mt_rand();
        $userId = new UserId(mt_rand());

        $deleteLocationCommand = new DeleteLocationCommand($locationId, $travelId, $userId);

        $this->locationRepository
            ->expects($this->once())
            ->method('findById')
            ->with($locationId)
            ->willReturn(null);

        $this->userRepository
            ->expects($this->once())
            ->method('ofIdOrFail')
            ->with($userId);

        $this->locationRepository
            ->expects($this->never())
            ->method('remove');

        $this->webSocketNotifier
            ->expects($this->never())
            ->method('notifyLocationRemoved');

        $deleteLocationService = new DeleteLocationService(
            $this->userRepository,
            $this->locationRepository,
            $this->webSocketNotifier
        );

        $deleteLocationService->handle($deleteLocationCommand);
    }
}
