<?php

namespace App\Application\UseCases\Travel;

use App\Application\Query\Travel\ShowTravelBySlugQuery;
use App\Application\UseCases\UsesCasesService;
use App\Domain\Travel\Exceptions\TravelDoesntExists;
use App\Domain\Travel\Model\Travel;
use App\Domain\Travel\Repository\TravelRepository;

class ShowTravelService implements UsesCasesService
{
    /** @var TravelRepository */
    private $travelRepository;

    /**
     * ShowTravelService constructor.
     *
     * @param TravelRepository $travelRepository
     */
    public function __construct(TravelRepository $travelRepository)
    {
        $this->travelRepository = $travelRepository;
    }

    public function __invoke(ShowTravelBySlugQuery $query): Travel
    {
        $travel = $this->travelRepository->ofSlugOrFail($query->getSlug());

        if (null === $travel) {
            throw new TravelDoesntExists();
        }

        return $travel;
    }
}
