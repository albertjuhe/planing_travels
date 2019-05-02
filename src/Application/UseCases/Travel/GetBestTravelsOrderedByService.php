<?php

namespace App\Application\UseCases\Travel;

use App\Application\Query\Travel\BestTravelsListQuery;
use App\Application\UseCases\usesCasesService;
use App\Domain\Travel\Repository\TravelReadModelRepository;

class GetBestTravelsOrderedByService implements usesCasesService
{
    private $travelReadModelRepository;

    public function __construct(TravelReadModelRepository $travelReadModelRepository)
    {
        $this->travelReadModelRepository = $travelReadModelRepository;
    }

    public function __invoke(BestTravelsListQuery $query)
    {
        $numberMaxOfTravels = $query->getNumberMaxOfTravels();
        $orderedBy = $query->getOrderedBy();

        return $this->travelReadModelRepository->getTravelOrderedBy($orderedBy, $numberMaxOfTravels);
    }
}
