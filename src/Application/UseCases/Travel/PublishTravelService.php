<?php
/**
 * Created by PhpStorm.
 * User: albert.juhe
 * Date: 24/10/2018
 * Time: 08:09
 */

namespace App\Application\UseCases\Travel;

use App\Application\Command\Travel\PublishTravelCommand;
use App\Domain\Travel\Exceptions\NotAllowedToPublishTravel;
use App\Domain\Travel\Repository\TravelRepository;
use App\Domain\Travel\Model\Travel;
use App\Domain\User\Model\User;
use App\Domain\User\Repository\UserRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class PublishTravelService
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
     * @param PublishTravelCommand $command
     * @return Travel
     * @throws \Exception
     */
    public function execute(PublishTravelCommand $command)
    {
        $travelSlug = $command->getTravelSlug();
        $user = $command->getUser();

        /** @var User $user */
        $user = $this->userRepository->ofIdOrFail($user->getUserId());
        /** @var Travel $travel */
        $travel = $this->travelRepository->ofSlugOrFail($travelSlug);

        /** var only the owner can publish it */
        if ($user->getUserId() != $travel->getUser()->getUserId()) throw new NotAllowedToPublishTravel();

        $travel->publish();
        $this->travelRepository->save($travel);

        /**
         * Publish travel

        $travelWasPublished = new TravelWasPublished($travel,$user);
        $this->eventDispatcher->dispatch(travelWasPublished::PUBLISH_TRAVEL_EVENT_REQUEST, $travelWasPublished);
*/

        return $travel;

    }

}