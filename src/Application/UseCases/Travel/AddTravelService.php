<?php


namespace App\Application\UseCases\Travel;

use App\Application\Command\Travel\AddTravelCommand;
use App\Domain\Travel\Repository\TravelRepository;
use App\Domain\Travel\Model\Travel;
use App\Domain\User\Repository\UserRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use App\Domain\Travel\Events\TravelWasAdded;

class AddTravelService
{
    /** @var TravelRepository; */
    private $travelRepository;
    /** @var UserRepository */
    private $userRepository;
    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /**
     * AddTravelService constructor.
     * @param TravelRepository $travelRepository
     * @param UserRepository $userRepository
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(TravelRepository $travelRepository,
                                UserRepository $userRepository,
                                EventDispatcherInterface $eventDispatcher)
    {
        $this->travelRepository = $travelRepository;
        $this->userRepository = $userRepository;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param AddTravelCommand $command
     * @return Travel
     * @throws \Exception
     */
    public function execute(AddTravelCommand $command)
    {
        $travel = $command->getTravel();
        $user = $command->getUser();

        $this->userRepository->ofIdOrFail($user->getUserId()->id());

        $travel->setUser($user);
        $this->travelRepository->save($travel);

        /**
         * After adding a travel we have to send info to ElasticSearch and RabitMq
        */
        $travelWasAdded = new TravelWasAdded($travel,$user);
        $this->eventDispatcher->dispatch(travelWasAdded::ADD_TRAVEL_EVENT_REQUEST, $travelWasAdded);

        return $travel;

    }
}
