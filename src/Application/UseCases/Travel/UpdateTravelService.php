<?php
/**
 * Created by PhpStorm.
 * User: albert.juhe
 * Date: 03/10/2018
 * Time: 07:17
 */

namespace App\Application\UseCases\Travel;


use App\Domain\Travel\Exceptions\InvalidTravelUser;
use App\Domain\User\Model\User;
use App\Domain\Travel\Repository\TravelRepository;
use App\Domain\Travel\Model\Travel;
use App\Application\Command\UpdateTravelCommand;

class UpdateTravelService
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

    public function execute(UpdateTravelCommand $commnand) {
        $travel = $commnand->travel();
        $user = $commnand->user();

        //Only the owner can modify the travel
        //TODO - Check the user Id, this is not the correct way
        if (!$travel->getUser()->getUserId()==$user->getUserId()) throw new InvalidTravelUser();

        $this->travelRepository->save($travel);
    }


}

