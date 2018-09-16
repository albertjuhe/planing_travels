<?php


namespace App\Application\UseCases\Travel;

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

    public function add(Travel $travel) {
        $this->travelRepository->save($travel);
    }
}