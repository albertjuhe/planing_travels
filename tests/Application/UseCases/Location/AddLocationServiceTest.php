<?php

namespace App\Tests\Application\UseCases\Location;

use App\Application\Command\Location\AddLocationCommand;
use App\Application\UseCases\Location\AddLocationService;
use App\Domain\Location\Model\Location;
use App\Domain\Mark\Model\Mark;
use App\Domain\Travel\Exceptions\InvalidTravelUser;
use App\Domain\Travel\Model\Travel;
use App\Domain\TypeLocation\Model\TypeLocation;
use App\Domain\User\Model\User;
use App\Domain\User\ValueObject\UserId;

class AddLocationServiceTest extends LocationServiceTest
{
    public function setUp()
    {
        parent::setUp();
    }

    public function testAddLocation(): void
    {
        $travelId = mt_rand();
        $userId = mt_rand();
        $locationTypeId = mt_rand();
        $location = $this->createMock(Location::class);
        $mark = $this->createMock(Mark::class);
        $typeLocation = $this->createMock(TypeLocation::class);

        $addLocationCommand = new AddLocationCommand(
            $travelId,
            $location,
            $userId,
            $mark,
            $locationTypeId
        );

        $user1 = $this->createMock(User::class);
        $user1->expects($this->once())->method('getId')->willReturn(new UserId($userId));

        $user2 = $this->createMock(User::class);
        $user2->expects($this->once())->method('getId')->willReturn(new UserId($userId));

        $travel = $this->createMock(Travel::class);
        $travel->expects($this->once())->method('getUser')->willReturn($user2);

        $this->userRepository->expects($this->once())->method('ofIdOrFail')->willReturn($user1);
        $this->travelRepository->expects($this->once())->method('ofIdOrFail')->willReturn($travel);

        $this->typeLocationRepository->expects($this->once())->method('idOrFail')->willReturn($typeLocation);
        $this->markRepository->expects($this->once())->method('ofIdOrSave')->willReturn($mark);

        $addLocationService = new AddLocationService(
            $this->travelRepository,
            $this->userRepository,
            $this->markRepository,
            $this->locationRepository,
            $this->typeLocationRepository
        );
        $location->expects($this->once())->method('setTravel')->with(
            $travel
        );
        $location->expects($this->once())->method('setMark')->with(
            $mark
        );
        $location->expects($this->once())->method('setTypeLocation')->with(
             $typeLocation
        );

        $this->locationRepository->expects($this->once())->method('save');

        $addLocationService->handle($addLocationCommand);
    }

    public function testAddLocationAndBelongUSerIsDifferent(): void
    {
        $this->expectException(InvalidTravelUser::class);

        $travelId = mt_rand();
        $userId = mt_rand();
        $locationTypeId = mt_rand();
        $location = $this->createMock(Location::class);
        $mark = $this->createMock(Mark::class);

        $addLocationCommand = new AddLocationCommand(
            $travelId,
            $location,
            $userId,
            $mark,
            $locationTypeId
        );

        $user1 = $this->createMock(User::class);
        $user1->expects($this->once())->method('getId')->willReturn(new UserId($userId));

        $user2 = $this->createMock(User::class);
        $user2->expects($this->once())->method('getId')->willReturn(new UserId($userId + 1));

        $travel = $this->createMock(Travel::class);
        $travel->expects($this->once())->method('getUser')->willReturn($user2);

        $this->userRepository->expects($this->once())->method('ofIdOrFail')->willReturn($user1);
        $this->travelRepository->expects($this->once())->method('ofIdOrFail')->willReturn($travel);

        $addLocationService = new AddLocationService(
            $this->travelRepository,
            $this->userRepository,
            $this->markRepository,
            $this->locationRepository,
            $this->typeLocationRepository
        );

        $this->locationRepository->expects($this->never())->method('save');

        $addLocationService->handle($addLocationCommand);
    }
}
