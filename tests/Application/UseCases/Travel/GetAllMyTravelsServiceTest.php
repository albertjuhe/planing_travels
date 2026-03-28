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

    public function testReturnsAllTravelsForUser(): void
    {
        $user = UserMother::random();
        $travel1 = TravelMother::random();
        $travel1->setUser($user);
        $travel2 = TravelMother::random();
        $travel2->setUser($user);
        $this->travelRepository->save($travel1);
        $this->travelRepository->save($travel2);

        $service = new GetAllMyTravelsService($this->travelRepository);
        $query = new GetMyTravelsQuery($user);

        $result = $service->__invoke($query);

        $this->assertCount(2, $result);
        $this->assertContainsOnlyInstancesOf(Travel::class, $result);
    }

    public function testReturnsEmptyArrayWhenUserHasNoTravels(): void
    {
        $user = UserMother::random();

        $service = new GetAllMyTravelsService($this->travelRepository);
        $query = new GetMyTravelsQuery($user);

        $result = $service->__invoke($query);

        $this->assertEmpty($result);
    }

    public function testDoesNotReturnTravelsFromOtherUsers(): void
    {
        $user = UserMother::random();
        $otherUser = UserMother::random();

        $travel = TravelMother::random();
        $travel->setUser($otherUser);
        $this->travelRepository->save($travel);

        $service = new GetAllMyTravelsService($this->travelRepository);
        $query = new GetMyTravelsQuery($user);

        $result = $service->__invoke($query);

        $this->assertEmpty($result);
    }
}
