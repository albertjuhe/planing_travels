<?php

namespace App\Application\UseCases\Travel;

use App\Domain\Travel\Model\Travel;
use App\Domain\Travel\Repository\IndexerRepository;
use App\Domain\Travel\Repository\TravelRepository;

class PopulateIndexer
{
    /**
     * @var TravelRepository
     */
    private $travelRepository;
    /**
     * @var IndexerRepository
     */
    private $indexerRepository;

    /**
     * PopulateIndexer constructor.
     */
    public function __construct(
        TravelRepository $travelRepository,
        IndexerRepository $indexerRepository
    ) {
        $this->travelRepository = $travelRepository;
        $this->indexerRepository = $indexerRepository;
    }

    public function execute()
    {
        $travels = $this->travelRepository->getAll();
        foreach ($travels as $travel) {
            /** @var $travel Travel */
            echo $travel->getId();
            $this->indexerRepository->save($travel);
        }
    }
}
