<?php

namespace App\Application\UseCases\Travel;

use App\Domain\Travel\Repository\TravelRepository;
use App\Domain\User\Model\User;

class GetAllMyTravelsService
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

    public function execute(User $user)
    {
        return $this->travelRepository->getAllTravelsByUser($user);
    }
}
