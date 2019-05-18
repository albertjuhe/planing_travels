<?php

namespace App\Tests\UseCases;

use App\Application\Query\Travel\GetMyTravelsQuery;
use App\Application\UseCases\Travel\GetAllMyTravelsService;
use App\Domain\User\Model\User;

class GetAllMyTravelsServiceTest extends ReadTravelServiceTest
{
    public function setUp()
    {
        parent::setUp();
    }

    public function testGetAllMyTravels()
    {
        $userId = 1;
        $travelId = mt_rand();

        $user = User::byId($userId);
        $travels = [
            [
                'id' => $travelId,
                'user' => $userId,
            ],
            [
                'id' => $travelId + 1,
                'user' => $userId,
            ],
        ];

        $getMyTravelQuery = new GetMyTravelsQuery($user);

        $this->travelRepository->method('getAllTravelsByUser')->willReturn($travels);
        $getAllMyTravelsService = new GetAllMyTravelsService($this->travelRepository);
        $myTravels = $getAllMyTravelsService->__invoke($getMyTravelQuery);

        foreach ($myTravels as $travel) {
            $this->assertEquals($travel['user'], $user->userId()->id());
        }

        $this->assertCount(2, $travels);
    }
}
