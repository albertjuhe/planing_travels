<?php


namespace App\Application\UseCases\Travel;

use App\Application\Command\Travel\AddTravelCommand;
use App\Domain\Travel\Repository\TravelRepository;
use App\Domain\Travel\Model\Travel;

class AddTravelService
{
    /**
     * @var TravelRepository;
     */
    private $travelRepository;

    /**
     * AddTravelService constructor.
     * @param TravelRepository $travelRepository
     */
    public function __construct(TravelRepository $travelRepository)
    {
        $this->travelRepository = $travelRepository;
    }

    /**
     * Add new travel
     * @param AddTravelCommand $commnand
     */
    public function execute(AddTravelCommand $commnand) {
        $travel = $commnand->getTravel();
        $this->travelRepository->save($travel);
    }
}