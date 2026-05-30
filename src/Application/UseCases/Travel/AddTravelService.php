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
    public function __construct(
        private readonly TravelRepository $travelRepository,
        private readonly UserRepository $userRepository,
    ) {
    }

    public function __invoke(AddTravelCommand $command)
    {
        $travel = $command->getTravel();
        $user = $command->getUser();

        $this->userRepository->ofIdOrFail($user->getId());

        $travel->setUser($user);

        if (!$travel->getSlug()) {
            $slugger = new AsciiSlugger();
            $slug = strtolower((string) $slugger->slug($travel->getTitle() ?? 'travel'));
            $travel->setSlug($slug ?: 'travel-' . uniqid());
        }

        $travel->record(new TravelWasAdded($travel->toArray()));
        DomainEventPublisher::instance()->publish(...$travel->pullDomainEvents());

        $this->travelRepository->save($travel);

        return $travel;
    }
}
