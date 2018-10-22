<?php


namespace App\Application\UseCases\Travel;

use App\Application\Command\Travel\AddTravelCommand;
use App\Domain\Travel\Repository\TravelRepository;
use App\Domain\Travel\Model\Travel;
use App\Domain\User\Repository\UserRepository;

class AddTravelService
{
    /** @var TravelRepository; */
    private $travelRepository;
    /** @var UserRepository */
    private $userRepository;

    /**
     * AddTravelService constructor.
     * @param TravelRepository $travelRepository
     */
    public function __construct(TravelRepository $travelRepository, UserRepository $userRepository)
    {
        $this->travelRepository = $travelRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @param AddTravelCommand $command
     * @return Travel
     */
    public function execute(AddTravelCommand $command) {
        $travel = $command->getTravel();
        $user = $command->getUser();

        $this->userRepository->ofIdOrFail($user->getUserId()->id());

        if (!($travel instanceof Travel)) throw new InvalidTravelCreation();

        $travel->setUser($user);
        $this->travelRepository->save($travel);

        return $travel;

    }
}