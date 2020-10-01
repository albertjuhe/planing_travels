<?php

namespace App\Tests\Application\UseCases\Travel;

use App\Application\Query\Travel\GetMyTravelsQuery;
use App\Application\UseCases\Travel\GetAllMyTravelsService;
use App\Tests\Domain\User\Model\UserMother;

class GetAllMyTravelsServiceTest extends ReadTravelService
{
    public function setUp()
    {
        parent::setUp();
    }

    public function testGetAllMyTravels()
    {
        $user = UserMother::random();
        $travels = $this->getTravels($user->getId()->id());

        $getMyTravelQuery = new GetMyTravelsQuery($user);

        $this->travelRepository->method('getAllTravelsByUser')->willReturn($travels);
        $getAllMyTravelsService = new GetAllMyTravelsService($this->travelRepository);
        $myTravels = $getAllMyTravelsService->__invoke($getMyTravelQuery);

        foreach ($myTravels as $travel) {
            $this->assertEquals($travel['user'], $user->userId()->id());
        }

        $this->assertCount(2, $travels);
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
