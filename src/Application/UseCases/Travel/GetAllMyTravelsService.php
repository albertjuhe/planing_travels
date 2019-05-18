<?php

namespace App\Application\UseCases\Travel;

use App\Application\Query\Travel\GetMyTravelsQuery;
use App\Domain\Travel\Repository\TravelReadModelRepository;

class GetAllMyTravelsService
{
    /**
     * @var TravelReadModelRepository;
     */
    private $travelRepository;

    public function __construct(TravelReadModelRepository $travelRepository)
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
