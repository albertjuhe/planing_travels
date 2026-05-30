<?php

namespace App\Application\UseCases\Travel;

use App\Application\UseCases\UsesCasesService;
use App\Domain\Event\DomainEventPublisher;
use App\Domain\Travel\Events\TravelWasUpdated;
use App\Domain\Travel\Exceptions\InvalidTravelUser;
use App\Domain\User\Model\User;
use App\Domain\Travel\Repository\TravelRepository;
use App\Domain\Travel\Model\Travel;
use App\Application\Command\Travel\UpdateTravelCommand;

class UpdateTravelService implements UsesCasesService
{
    public function __construct(
        private readonly TravelRepository $travelRepository,
    ) {
    }

    public function __invoke(UpdateTravelCommand $command)
    {
        $travel = $command->travel();
        $user = $command->user();

        $isOwner = $travel->getUser()->getId()->equalsTo($user->getId());
        $isSharedUser = false;
        foreach ($travel->getSharedusers() as $sharedUser) {
            if ($sharedUser->getId()->equalsTo($user->getId())) {
                $isSharedUser = true;
                break;
            }
        }

        if (!$isOwner && !$isSharedUser) {
            throw new InvalidTravelUser();
        }

        $travel->record(new TravelWasUpdated($travel->toArray()));
        DomainEventPublisher::instance()->publish(...$travel->pullDomainEvents());

        $this->travelRepository->save($travel);
    }
}
