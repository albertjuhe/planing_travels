<?php

namespace App\Application\UseCases\Travel;

use App\Application\Query\Travel\GetMyTravelsQuery;
use App\Domain\Travel\Repository\TravelRepository;

class GetAllMyTravelsService
{
    /** @var TravelRepository */
    private $travelRepository;

    public function __construct(TravelRepository $travelRepository)
    {
        $this->travelRepository = $travelRepository;
    }

    public function __invoke(GetMyTravelsQuery $getMyTravelsQuery)
    {
        $user   = $getMyTravelsQuery->getUser();
        $userId = $user->userId()->id();

        $owned  = $this->travelRepository->getAllTravelsByUser($userId);
        $shared = $this->travelRepository->getSharedTravelsByUser($userId);

        return [
            'owned'  => $owned,
            'shared' => $shared,
        ];
    }
}
