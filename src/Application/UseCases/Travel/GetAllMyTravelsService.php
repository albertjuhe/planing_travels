<?php

namespace App\Application\UseCases\Travel;

use App\Application\Query\Travel\GetMyTravelsQuery;
use App\Domain\Travel\Repository\TravelReadModelRepository;
use App\Domain\User\Repository\UserRepository;
use App\Domain\User\ValueObject\UserId;

class GetAllMyTravelsService
{
    /**
     * @var TravelReadModelRepository;
     */
    private $travelRepository;
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(TravelReadModelRepository $travelRepository, UserRepository $userRepository)
    {
        $this->travelRepository = $travelRepository;
        $this->userRepository = $userRepository;
    }

    public function __invoke(GetMyTravelsQuery $getMyTravelsQuery)
    {
        $userId = $getMyTravelsQuery->getUser();

        $this->userRepository->ofIdOrFail(new UserId($userId));

        return $this->travelRepository->getAllTravelsByUser(
            $userId
        );
    }
}
