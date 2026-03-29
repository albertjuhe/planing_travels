<?php

namespace App\Application\UseCases\Travel;

use App\Application\Command\Travel\UnpublishTravelCommand;
use App\Application\UseCases\UsesCasesService;
use App\Domain\Travel\Exceptions\NotAllowedToPublishTravel;
use App\Domain\Travel\Repository\TravelRepository;
use App\Domain\User\Repository\UserRepository;

class UnpublishTravelService implements UsesCasesService
{
    private $travelRepository;
    private $userRepository;

    public function __construct(TravelRepository $travelRepository, UserRepository $userRepository)
    {
        $this->travelRepository = $travelRepository;
        $this->userRepository = $userRepository;
    }

    public function handle(UnpublishTravelCommand $command): void
    {
        $user = $this->userRepository->ofIdOrFail($command->getUser()->getId());
        $travel = $this->travelRepository->ofSlugOrFail($command->getTravelSlug());

        if (!$user->getId()->equalsTo($travel->getUser()->getId())) {
            throw new NotAllowedToPublishTravel();
        }

        $travel->unpublish();
        $this->travelRepository->save($travel);
    }
}
