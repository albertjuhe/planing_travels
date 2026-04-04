<?php

namespace App\Tests\Application\UseCases\Travel;

use App\Application\Query\Travel\GetMyTravelsQuery;
use App\Application\UseCases\Travel\GetAllMyTravelsService;
use App\Domain\Travel\Model\Travel;
use App\Tests\Domain\Travel\Model\TravelMother;
use App\Tests\Domain\User\Model\UserMother;

class GetAllMyTravelsServiceTest extends TravelService
{
    public function setUp()
    {
        parent::setUp();
    }

    public function testReturnsOwnedTravelsForUser(): void
    {
        $user = UserMother::random();
        $travel1 = TravelMother::random();
        $travel1->setUser($user);
        $travel2 = TravelMother::random();
        $travel2->setUser($user);
        $this->travelRepository->save($travel1);
        $this->travelRepository->save($travel2);

        $service = new GetAllMyTravelsService($this->travelRepository);
        $result = $service->__invoke(new GetMyTravelsQuery($user));

        $this->assertArrayHasKey('owned', $result);
        $this->assertArrayHasKey('shared', $result);
        $this->assertCount(2, $result['owned']);
        $this->assertContainsOnlyInstancesOf(Travel::class, $result['owned']);
    }

    public function testReturnsEmptyOwnedWhenUserHasNoTravels(): void
    {
        $user = UserMother::random();

        $service = new GetAllMyTravelsService($this->travelRepository);
        $result = $service->__invoke(new GetMyTravelsQuery($user));

        $this->assertEmpty($result['owned']);
        $this->assertEmpty($result['shared']);
    }

    public function testDoesNotReturnOtherUsersTravelsAsOwned(): void
    {
        $user = UserMother::random();
        $otherUser = UserMother::random();
        $travel = TravelMother::random();
        $travel->setUser($otherUser);
        $this->travelRepository->save($travel);

        $service = new GetAllMyTravelsService($this->travelRepository);
        $result = $service->__invoke(new GetMyTravelsQuery($user));

        $this->assertEmpty($result['owned']);
    }

    public function testReturnsSharedTravelsForUser(): void
    {
        $owner = UserMother::random();
        $sharedUser = UserMother::random();

        $travel = TravelMother::random();
        $travel->setUser($owner);
        $sharedUser->addTravelsshared($travel);
        $this->travelRepository->save($travel);

        $service = new GetAllMyTravelsService($this->travelRepository);
        $result = $service->__invoke(new GetMyTravelsQuery($sharedUser));

        $this->assertEmpty($result['owned']);
        $this->assertCount(1, $result['shared']);
        $this->assertSame($travel, $result['shared'][0]);
    }

    public function testOwnedTravelsNotReturnedAsShared(): void
    {
        $user = UserMother::random();
        $travel = TravelMother::random();
        $travel->setUser($user);
        $user->addTravelsshared($travel);
        $this->travelRepository->save($travel);

        $service = new GetAllMyTravelsService($this->travelRepository);
        $result = $service->__invoke(new GetMyTravelsQuery($user));

        $this->assertCount(1, $result['owned']);
        $this->assertEmpty($result['shared']);
    }
}
