<?php

namespace App\Tests\Application\UseCases\Travel;

use App\Tests\Infrastructure\TravelBundle\Repository\InMemoryTravelRepository;

class GetBestTravelsOrderedByServiceTest
{
    /** @var InMemoryTravelRepository */
    private $travelRepository;

    public function setUp()
    {
        $this->travelRepository = new InMemoryTravelRepository();
        $this->travelRepository->loadData();
    }

    public function testGetBestTravelsOrderedByService()
    {
    }
}
