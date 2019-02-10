<?php


namespace App\Application\UseCases\Location;


use App\Application\Command\Location\AddLocationCommand;
use App\Application\UseCases\UsesCasesService;
use App\Domain\Location\Events\LocationWasAdded;
use App\Domain\Location\Repository\LocationRepository;
use App\Domain\Mark\Repository\MarkRepository;
use App\Domain\Travel\Exceptions\InvalidTravelUser;
use App\Domain\Travel\Exceptions\TravelDoesntExists;
use App\Domain\Travel\Model\Travel;
use App\Domain\Travel\Repository\TravelRepository;
use App\Domain\User\Exceptions\UserDoesntExists;
use App\Domain\User\Model\User;
use App\Domain\User\Repository\UserRepository;

class AddLocationService implements UsesCasesService
{
    /**
     * @var TravelRepository;
     */
    private $travelRepository;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var MarkRepository
     */
    private $markRepository;
    /**
     * @var LocationRepository
     */
    private $locationRepository;


    public function __construct(
        TravelRepository $travelRepository,
        UserRepository $userRepository,
        MarkRepository $markRepository,
        LocationRepository $locationRepository

    )
    {
        $this->travelRepository = $travelRepository;
        $this->userRepository = $userRepository;
        $this->markRepository = $markRepository;
        $this->locationRepository = $locationRepository;
    }


    public function handle(AddLocationCommand $addLocationCommand)
    {
        $travelId = $addLocationCommand->getTravelId();
        $location = $addLocationCommand->getLocation();
        $userId = $addLocationCommand->getUser();
        $mark = $addLocationCommand->getMark();

        $user = $this->userRepository->ofIdOrFail($userId);
        if (!$user instanceof User)
            throw new UserDoesntExists('User doesnt exists');

        $travel = $this->travelRepository->ofIdOrFail($travelId);
        if (!$travel instanceof Travel)
            throw new TravelDoesntExists('Travel doesnt exists');

        if ($travel->getUser()->equal($user))
            throw new InvalidTravelUser('This user is not allowed to modify the travel');

        //find the mark if not exists create it
        $mark = $this->markRepository->ofIdOrSave($mark->getId());

        $location->setUser($user);
        $location->setTravel($travel);
        $location->setMark($mark);

        DomainEventPublisher::instance()->publish(new LocationWasAdded($location));
        $this->locationRepository->save($location);
    }


}