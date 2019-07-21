<?php

namespace App\Application\UseCases\Travel;

use App\Domain\Travel\Model\Travel;
use App\Domain\Travel\Repository\IndexerRepository;
use App\Domain\Travel\Repository\TravelRepository;

class PopulateIndexer
{
    private $travelRepository;
    private $indexerRepository;

    public function __construct(
        TravelRepository $travelRepository,
        IndexerRepository $indexerRepository
    ) {
        $this->travelRepository = $travelRepository;
        $this->indexerRepository = $indexerRepository;
    }

    public function execute(): void
    {
        $travels = $this->travelRepository->getAll();
        foreach ($travels as $travel) {
            /* @var $travel Travel */
            $this->indexerRepository->save($travel);
        }
    }
}
