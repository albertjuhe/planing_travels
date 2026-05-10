<?php

namespace App\Tests\Application\UseCases\Travel;

use App\Application\UseCases\Travel\PopulateIndexer;
use App\Domain\Travel\Model\Travel;
use App\Domain\Travel\Repository\IndexerRepository;
use App\Domain\Travel\Repository\TravelRepository;
use PHPUnit\Framework\TestCase;

class PopulateIndexerTest extends TestCase
{
    private TravelRepository $travelRepository;
    private IndexerRepository $indexerRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->travelRepository = $this->createMock(TravelRepository::class);
        $this->indexerRepository = $this->createMock(IndexerRepository::class);
    }

    public function testExecuteIndexesAllTravels(): void
    {
        $travel1 = $this->createMock(Travel::class);
        $travel2 = $this->createMock(Travel::class);
        $travels = [$travel1, $travel2];

        $this->travelRepository
            ->expects($this->once())
            ->method('getAll')
            ->willReturn($travels);

        $savedTravels = [];
        $this->indexerRepository
            ->expects($this->exactly(2))
            ->method('save')
            ->willReturnCallback(function (Travel $travel) use (&$savedTravels) {
                $savedTravels[] = $travel;
            });

        $populateIndexer = new PopulateIndexer($this->travelRepository, $this->indexerRepository);
        $populateIndexer->execute();

        $this->assertSame($travel1, $savedTravels[0]);
        $this->assertSame($travel2, $savedTravels[1]);
    }

    public function testExecuteWithEmptyRepositoryDoesNotCallSave(): void
    {
        $this->travelRepository
            ->expects($this->once())
            ->method('getAll')
            ->willReturn([]);

        $this->indexerRepository
            ->expects($this->never())
            ->method('save');

        $populateIndexer = new PopulateIndexer($this->travelRepository, $this->indexerRepository);
        $populateIndexer->execute();
    }

    public function testExecuteIndexesSingleTravel(): void
    {
        $travel = $this->createMock(Travel::class);

        $this->travelRepository
            ->expects($this->once())
            ->method('getAll')
            ->willReturn([$travel]);

        $this->indexerRepository
            ->expects($this->once())
            ->method('save')
            ->with($travel);

        $populateIndexer = new PopulateIndexer($this->travelRepository, $this->indexerRepository);
        $populateIndexer->execute();
    }
}
