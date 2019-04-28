<?php

namespace App\Tests\UseCases;

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
