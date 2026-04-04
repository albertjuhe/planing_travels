<?php

namespace App\Application\UseCases\Travel;

use App\Application\Command\Travel\ShareTravelCommand;
use App\Application\UseCases\UsesCasesService;
use App\Domain\Travel\Exceptions\InvalidTravelUser;
use App\Domain\Travel\Repository\TravelRepository;
use App\Domain\User\Exceptions\UserDoesntExists;
use App\Domain\User\Repository\UserRepository;
use App\Domain\User\ValueObject\UserId;

class ShareTravelService implements UsesCasesService
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

    public function handle(ShareTravelCommand $command): void
    {
        $travel = $this->travelRepository->ofIdOrFail($command->getTravelId());

        if ((int) $travel->getUser()->getId()->id() !== $command->getOwnerUserId()) {
            throw new InvalidTravelUser('Only the travel owner can share it');
        }

        $targetUser = $this->userRepository->UserByUsername($command->getTargetUsername());

        if (null === $targetUser) {
            throw new UserDoesntExists('User "'.$command->getTargetUsername().'" does not exist');
        }

        $alreadyShared = $travel->getSharedusers()->exists(
            function ($key, $sharedUser) use ($targetUser) {
                return $sharedUser->getId()->equalsTo($targetUser->getId());
            }
        );

        if ($alreadyShared) {
            return;
        }

        $travel->addShareduser($targetUser);
        $targetUser->addTravelsshared($travel);
        $this->userRepository->save($targetUser);
    }
}
