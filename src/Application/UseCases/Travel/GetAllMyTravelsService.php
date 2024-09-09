<?php

namespace App\Application\UseCases\Travel;

use App\Application\Query\Travel\GetMyTravelsQuery;
use App\Domain\Travel\Model\Travel;
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
        $travels = [];

        $user = $getMyTravelsQuery->getUser();

        $travelsResult = $this->travelRepository->getAllTravelsByUser(
            $user->userId()->id()
        );

        /** @var Travel $travel */
        foreach ($travelsResult as $travel) {
            $travels[] = $travel->toArray();
        }

        return $travels;
    }
}
