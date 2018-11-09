<?php


namespace App\Application\UseCases\Travel;

use App\Application\Command\Travel\AddTravelCommand;
use App\Domain\Event\DomainEventPublisher;
use App\Domain\Travel\Events\TravelWasAdded;
use App\Domain\Travel\Repository\TravelRepository;
use App\Domain\Travel\Model\Travel;
use App\Domain\User\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Tests\Fixtures\User;

class AddTravelService
{
    /** @var TravelRepository; */
    private $travelRepository;
    /** @var UserRepository */
    private $userRepository;

    /**
     * AddTravelService constructor.
     * @param TravelRepository $travelRepository
     * @param UserRepository $userRepository
     */
    public function __construct(TravelRepository $travelRepository,
                                UserRepository $userRepository)
    {
        $this->travelRepository = $travelRepository;
        $this->userRepository = $userRepository;

    }

    /**
     * @param AddTravelCommand $command
     * @return Travel
     * @throws \Exception
     */
    public function handle(AddTravelCommand $command)
    {
        /** @var Travel $travel */
        $travel = $command->getTravel();
        /** @var User $user */
        $user = $command->getUser();

        $this->userRepository->ofIdOrFail($user->getUserId());

        $travel->setUser($user);
        DomainEventPublisher::instance()->publish(new TravelWasAdded($travel));
        $this->travelRepository->save($travel);

        return $travel;
    }
}
