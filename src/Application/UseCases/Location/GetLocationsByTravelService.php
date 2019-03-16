<?php

namespace App\Application\UseCases\Location;

use App\Application\Command\Location\GetLocationsByTravelCommand;
use App\Application\UseCases\UsesCasesService;
use App\Domain\Travel\DataTransformer\LocationsTravelArrayDataTransformer;
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
    public function handle(GetLocationsByTravelCommand $command)
    {
        $travelId = $command->getTravel();
        /** @var Travel $travel */
        $travel = $this->travelRepository->ofIdOrFail($travelId);

        $locations = [];
        $locationsTravelArrayDataTransformer = new LocationsTravelArrayDataTransformer();

        foreach ($travel->getLocation()->getValues() as $location) {
            $locationsTravelArrayDataTransformer->write($location);
            $locations[] = $locationsTravelArrayDataTransformer->read();
        }

        return $locations;
    }
}
