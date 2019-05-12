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
        $user = User::byId(1);
        $getMyTravelQuery = new GetMyTravelsQuery($user);
        $getAllMyTravelsService = new GetAllMyTravelsService($this->travelRepository);
        $travels = $getAllMyTravelsService->__invoke($getMyTravelQuery);

        foreach ($travels as $travel) {
            $this->assertEquals($travel->getUser()->userId(), $user->userId());
        }

        $this->assertCount(3, $travels);
    }
}
