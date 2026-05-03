<?php

namespace App\Tests\Application\UseCases\Travel;

use App\Application\Query\Travel\ShowTravelBySlugQuery;
use App\Application\UseCases\Travel\ShowTravelService;
use App\Domain\Travel\Exceptions\TravelDoesntExists;
use App\Domain\Travel\Model\Travel;
use App\Tests\Infrastructure\TravelBundle\Repository\InMemoryTravelRepository;
use Doctrine\ORM\EntityManagerInterface;

class ShowTravelServiceTest extends TravelService
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testShowTravelBySlug(): void
    {
        $showTravelBySlugQuery = new ShowTravelBySlugQuery(InMemoryTravelRepository::TRAVEL_1);
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $showTravelService = new ShowTravelService(
            $this->travelRepository,
            $entityManager
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
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $showTravelService = new ShowTravelService(
            $this->travelRepository,
            $entityManager
        );

        $showTravelService->__invoke($showTravelBySlugQuery);
    }
}
