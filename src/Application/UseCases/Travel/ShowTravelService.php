<?php

namespace App\Application\UseCases\Travel;

use App\Application\Query\Travel\ShowTravelBySlugQuery;
use App\Application\UseCases\UsesCasesService;
use App\Domain\Travel\Exceptions\TravelDoesntExists;
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

    public function __invoke(ShowTravelBySlugQuery $query)
    {
        if (null === $query->getSlug()) {
            throw new TravelDoesntExists();
        }

        return $this->travelRepository->ofSlugOrFail($query->getSlug());
    }
}
