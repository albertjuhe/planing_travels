<?php

namespace App\Application\UseCases\Travel;

use App\Application\Command\Travel\AddTravelCommand;
use App\Application\UseCases\UsesCasesService;
use App\Domain\Event\DomainEventPublisher;
use App\Domain\Travel\Events\TravelWasAdded;
use App\Domain\Travel\Repository\TravelRepository;
use App\Domain\Travel\Model\Travel;
use App\Domain\User\Repository\UserRepository;
use Symfony\Component\String\Slugger\AsciiSlugger;

class AddTravelService implements UsesCasesService
{
    /** @var TravelRepository; */
    private $travelRepository;
    /** @var UserRepository */
    private $userRepository;

    public function __construct(
        TravelRepository $travelRepository,
        UserRepository $userRepository
    ) {
        $this->travelRepository = $travelRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @param AddTravelCommand $command
     *
     * @return Travel
     *
     * @throws \Exception
     */
    public function __invoke(AddTravelCommand $command)
    {
        /** @var Travel $travel */
        $travel = $command->getTravel();
        /** @var User $user */
        $user = $command->getUser();

        $this->userRepository->ofIdOrFail($user->getId());

        $travel->setUser($user);

        if (!$travel->getSlug()) {
            $slugger = new AsciiSlugger();
            $slug = strtolower((string) $slugger->slug($travel->getTitle() ?? 'travel'));
            $travel->setSlug($slug ?: 'travel-' . uniqid());
        }

        DomainEventPublisher::instance()->publish(new TravelWasAdded($travel->toArray()));
        $this->travelRepository->save($travel);

        return $travel;
    }
}
