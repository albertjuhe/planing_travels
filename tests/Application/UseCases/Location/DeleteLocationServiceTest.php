<?php

namespace App\Tests\Application\UseCases\Location;

use App\Application\Command\Location\DeleteLocationCommand;
use App\Application\UseCases\Location\DeleteLocationService;
use App\Domain\Location\Model\Location;
use App\Domain\User\ValueObject\UserId;
use App\Domain\Location\Exceptions\LocationDoesntExists;

class DeleteLocationServiceTest extends LocationService
{
    public function setUp()
    {
        parent::setUp();
    }

    public function testDeleteLocation(): void
    {
        $locationId = mt_rand();
        $travelId = mt_rand();

        $userId = new UserId(mt_rand());

        $deleteLocationCommand = new DeleteLocationCommand(
            $locationId,
            $travelId,
            $userId
        );

        $location = $this->createMock(Location::class);
        $this->locationRepository
            ->expects($this->once())
            ->method('findById')
            ->with($locationId)
            ->willReturn($location);

        $this->userRepository
            ->expects($this->once())
            ->method('ofIdOrFail')
            ->with($userId);

        $this->locationRepository
            ->expects($this->once())
            ->method('remove')
            ->with($location);

        $deleteLocationService = new DeleteLocationService(
            $this->userRepository,
            $this->locationRepository
        );

        $deleteLocationService->handle($deleteLocationCommand);
    }

    public function testDeleteNonExistedLocation(): void
    {
        $this->expectException(LocationDoesntExists::class);
        $locationId = mt_rand();
        $travelId = mt_rand();

        $userId = new UserId(mt_rand());

        $deleteLocationCommand = new DeleteLocationCommand(
            $locationId,
            $travelId,
            $userId
        );

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

        $deleteLocationService = new DeleteLocationService(
            $this->userRepository,
            $this->locationRepository
        );

        $deleteLocationService->handle($deleteLocationCommand);
    }
}
