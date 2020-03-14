<?php

namespace App\Application\UseCases\Location;

use App\Application\Query\Location\GetLocationsByTravelQuery;
use App\Application\UseCases\UsesCasesService;
use App\Domain\Travel\Exceptions\TravelDoesntExists;
use App\Domain\Travel\Model\Travel;
use App\Domain\Travel\Repository\TravelRepository;

class GetLocationsByTravelService implements usesCasesService
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

    /**
     * @return mixed
     */
    public function __invoke(GetLocationsByTravelQuery $query)
    {
        $travelId = $query->getTravel();
        /** @var Travel $travel */
        $travel = $this->travelRepository->ofIdOrFail($travelId);

        if (!$travel instanceof Travel) {
            throw new TravelDoesntExists();
        }

        $locations = [];

        foreach ($travel->getLocation()->getValues() as $location) {
            $locations[] = $location->toArray();
        }

        return $locations;
    }
}
