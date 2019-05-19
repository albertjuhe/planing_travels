<?php

namespace App\Application\UseCases\Travel;

use App\Application\Command\Travel\PublishTravelCommand;
use App\Application\UseCases\UsesCasesService;
use App\Domain\Event\DomainEventPublisher;
use App\Domain\Travel\Exceptions\NotAllowedToPublishTravel;
use App\Domain\Travel\Repository\TravelRepository;
use App\Domain\Travel\Model\Travel;
use App\Domain\User\Model\User;
use App\Domain\User\Repository\UserRepository;

class PublishTravelService implements UsesCasesService
{
    /** @var TravelRepository; */
    private $travelRepository;
    /** @var UserRepository */
    private $userRepository;

    /**
     * AddTravelService constructor.
     *
     * @param TravelRepository $travelRepository
     * @param UserRepository   $userRepository
     */
    public function __construct(
        TravelRepository $travelRepository,
        UserRepository $userRepository
    ) {
        $this->travelRepository = $travelRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @param PublishTravelCommand $command
     *
     * @return Travel
     *
     * @throws \Exception
     */
    public function handle(PublishTravelCommand $command)
    {
        $travelSlug = $command->getTravelSlug();
        $user = $command->getUser();

        /** @var User $user */
        $user = $this->userRepository->ofIdOrFail($user->getId());
        /** @var Travel $travel */
        $travel = $this->travelRepository->ofSlugOrFail($travelSlug);

        /* var only the owner can publish it */
        if (!$user->getId()->equalsTo($travel->getUser()->getId())) {
            throw new NotAllowedToPublishTravel();
        }
        $travel->publish();

        DomainEventPublisher::instance()->publish(...$travel->pullDomainEvents());
        $this->travelRepository->save($travel);

        return $travel;
    }
}
