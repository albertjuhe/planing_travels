<?php

namespace App\Application\UseCases\Location;

use App\Application\Command\Location\GetLocationsByTravelCommand;
use App\Application\UseCases\UsesCasesService;
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
    public function handle(GetLocationsByTravelCommand $command): array
    {
        $travelId = $command->getTravel();

        $travel = $this->travelRepository->ofIdOrFail($travelId);

        return $travel->getLocation();
    }
}
