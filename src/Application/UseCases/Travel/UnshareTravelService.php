<?php

namespace App\Application\UseCases\Travel;

use App\Application\Command\Travel\UnshareTravelCommand;
use App\Application\UseCases\UsesCasesService;
use App\Domain\Travel\Exceptions\InvalidTravelUser;
use App\Domain\Travel\Repository\TravelRepository;
use App\Domain\User\Exceptions\UserDoesntExists;
use App\Domain\User\Repository\UserRepository;

class UnshareTravelService implements UsesCasesService
{
    /** @var TravelRepository */
    private $travelRepository;

    /** @var UserRepository */
    private $userRepository;

    public function __construct(TravelRepository $travelRepository, UserRepository $userRepository)
    {
        $this->travelRepository = $travelRepository;
        $this->userRepository = $userRepository;
    }

    public function handle(UnshareTravelCommand $command): void
    {
        $travel = $this->travelRepository->ofIdOrFail($command->getTravelId());

        if ((int) $travel->getUser()->getId()->id() !== $command->getOwnerUserId()) {
            throw new InvalidTravelUser('Only the travel owner can unshare it');
        }

        $targetUser = $this->userRepository->UserByUsername($command->getTargetUsername());

        if (null === $targetUser) {
            throw new UserDoesntExists('User "'.$command->getTargetUsername().'" does not exist');
        }

        $travel->removeShareduser($targetUser);
        $targetUser->removeTravelsshared($travel);
        $this->userRepository->save($targetUser);
    }
}
