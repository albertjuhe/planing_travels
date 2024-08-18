<?php

namespace App\Application\UseCases\Travel;

use App\Application\Query\Travel\GetMyTravelsQuery;
use App\Domain\Travel\Repository\TravelReadModelRepository;
use App\Domain\Travel\Repository\TravelRepository;

class GetAllMyTravelsService
{
    /**
     * @var TravelRepository;
     */
    private $travelRepository;

    public function __construct(TravelRepository $travelRepository)
    {
        $this->travelRepository = $travelRepository;
    }

    public function __invoke(GetMyTravelsQuery $getMyTravelsQuery)
    {
        $user = $getMyTravelsQuery->getUser();

        return $this->travelRepository->getAllTravelsByUser(
            $user->userId()->id()
        );
    }
}
