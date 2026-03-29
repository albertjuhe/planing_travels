<?php

namespace App\Tests\Application\UseCases\Travel;

use App\Application\UseCases\Travel\PopulateIndexer;
use App\Domain\Travel\Model\Travel;
use App\Domain\Travel\Repository\IndexerRepository;
use App\Domain\Travel\Repository\TravelRepository;
use PHPUnit\Framework\TestCase;

class PopulateIndexerTest extends TestCase
{
    public function testExecuteIndexesAllTravels(): void
    {
        $travel1 = $this->createMock(Travel::class);
        $travel2 = $this->createMock(Travel::class);
        $travels = [$travel1, $travel2];

        $travelRepository = $this->createMock(TravelRepository::class);
        $travelRepository->expects($this->once())
            ->method('getAll')
            ->willReturn($travels);

        $indexerRepository = $this->createMock(IndexerRepository::class);
        $indexerRepository->expects($this->exactly(2))
            ->method('save')
            ->withConsecutive([$travel1], [$travel2]);

        $populateIndexer = new PopulateIndexer($travelRepository, $indexerRepository);
        $populateIndexer->execute();
    }

    public function testExecuteWithNoTravelsDoesNotCallSave(): void
    {
        $travelRepository = $this->createMock(TravelRepository::class);
        $travelRepository->expects($this->once())
            ->method('getAll')
            ->willReturn([]);

        $indexerRepository = $this->createMock(IndexerRepository::class);
        $indexerRepository->expects($this->never())
            ->method('save');

        $populateIndexer = new PopulateIndexer($travelRepository, $indexerRepository);
        $populateIndexer->execute();
    }
}
