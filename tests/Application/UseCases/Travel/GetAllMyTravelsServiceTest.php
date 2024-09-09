<?php

namespace App\Tests\Application\UseCases\Travel;

use App\Application\Query\Travel\GetMyTravelsQuery;
use App\Application\UseCases\Travel\GetAllMyTravelsService;
use App\Tests\Domain\Travel\Model\TravelMother;
use App\Tests\Domain\User\Model\UserMother;

class GetAllMyTravelsServiceTest extends TravelService
{
    public function setUp()
    {
        parent::setUp();
    }

    public function testGetAllMyTravels()
    {
        $user = UserMother::random();

        $travel = TravelMother::withUser($user);
        $travelId = $travel->getTravelUniqId();
        $this->travelRepository->save($travel);

        $getMyTravelQuery = new GetMyTravelsQuery($user);

        $getAllMyTravelsService = new GetAllMyTravelsService($this->travelRepository);
        $myTravels = $getAllMyTravelsService->__invoke($getMyTravelQuery);

        foreach ($myTravels as $travel) {
            $this->assertEquals($travel['userId'], $user->userId()->id());
            $this->assertEquals($travel['id'], $travelId);
        }

        $this->assertCount(1, $myTravels);
    }

    private function getTravels(int $userId): array
    {
        $travelId = mt_rand();

        return [
            [
                'id' => $travelId,
                'user' => $userId,
            ],
            [
                'id' => $travelId + 1,
                'user' => $userId,
            ],
        ];
    }
}
