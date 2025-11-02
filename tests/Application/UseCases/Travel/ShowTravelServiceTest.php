<?php

namespace App\Tests\Application\UseCases\Travel;

use App\Application\Query\Travel\ShowTravelBySlugQuery;
use App\Application\UseCases\Travel\ShowTravelService;
use App\Domain\Travel\Exceptions\TravelDoesntExists;
use App\Domain\Travel\Model\Travel;
use App\Tests\Infrastructure\TravelBundle\Repository\InMemoryTravelRepository;

class ShowTravelServiceTest extends TravelService
{
    public function setUp()
    {
        parent::setUp();
    }

    public function testShowTravelBySlug(): void
    {
        $showTravelBySlugQuery = new ShowTravelBySlugQuery(InMemoryTravelRepository::TRAVEL_1);
        $showTravelService = new ShowTravelService(
            $this->travelRepository
        );

        $travel = $showTravelService->__invoke($showTravelBySlugQuery);

        $this->assertInstanceOf(Travel::class, $travel);
        $this->assertEquals($travel->getSlug(), InMemoryTravelRepository::TRAVEL_1);
        $this->assertEquals($travel->getTitle(), InMemoryTravelRepository::TRAVEL_1);
    }

    public function testDoesntExistsTheTravelBySlug(): void
    {
        $this->expectException(TravelDoesntExists::class);
        $showTravelBySlugQuery = new ShowTravelBySlugQuery(uniqid());
        $showTravelService = new ShowTravelService(
            $this->travelRepository
        );

        $showTravelService->__invoke($showTravelBySlugQuery);
    }
}
