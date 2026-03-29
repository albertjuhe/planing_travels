<?php

namespace App\Tests\Application\UseCases\Travel;

use App\Application\Query\Travel\BestTravelsListQuery;
use App\Application\UseCases\Travel\GetBestTravelsOrderedByService;
use App\Domain\Travel\Repository\TravelReadModelRepository;
use PHPUnit\Framework\TestCase;

class GetBestTravelsOrderedByServiceTest extends TestCase
{
    public function testReturnsResultsFromRepository(): void
    {
        $expectedTravels = [
            ['id' => 'abc', 'title' => 'Toscana', 'stars' => 5, 'status' => 20],
            ['id' => 'def', 'title' => 'Creta',   'stars' => 4, 'status' => 20],
        ];

        $repository = $this->createMock(TravelReadModelRepository::class);
        $repository->expects($this->once())
            ->method('getTravelOrderedBy')
            ->with('stars', 10)
            ->willReturn($expectedTravels);

        $service = new GetBestTravelsOrderedByService($repository);
        $result  = $service(new BestTravelsListQuery(10, 'stars'));

        $this->assertSame($expectedTravels, $result);
    }

    public function testPassesMaxResultsAndOrderToRepository(): void
    {
        $repository = $this->createMock(TravelReadModelRepository::class);
        $repository->expects($this->once())
            ->method('getTravelOrderedBy')
            ->with('watch', 5)
            ->willReturn([]);

        $service = new GetBestTravelsOrderedByService($repository);
        $result  = $service(new BestTravelsListQuery(5, 'watch'));

        $this->assertSame([], $result);
    }

    public function testReturnsEmptyArrayWhenNoPublishedTravels(): void
    {
        $repository = $this->createMock(TravelReadModelRepository::class);
        $repository->method('getTravelOrderedBy')->willReturn([]);

        $service = new GetBestTravelsOrderedByService($repository);
        $result  = $service(new BestTravelsListQuery(10, 'stars'));

        $this->assertEmpty($result);
    }
}
