<?php

namespace App\Application\UseCases\Travel;

use App\Application\Query\Travel\BestTravelsListQuery;
use App\Application\UseCases\usesCasesService;
use App\Domain\Travel\Repository\TravelRepository;

class GetBestTravelsOrderedByService implements usesCasesService
{
    /**
     * @var TravelRepository;
     */
    private $travelRepository;

    /**
     * GetAllMyTravels constructor.
     *
     * @param TravelRepository $travelRepository
     */
    public function __construct(TravelRepository $travelRepository)
    {
        $this->travelRepository = $travelRepository;
    }

    public function __invoke(BestTravelsListQuery $query)
    {
        $numberMaxOfTravels = $query->getNumberMaxOfTravels();
        $orderedBy = $query->getOrderedBy();

        return $this->travelRepository->TravelsAllOrderedBy($numberMaxOfTravels);
    }
}
