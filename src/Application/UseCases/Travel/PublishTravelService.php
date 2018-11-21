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

class PublishTravelService
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
                                UserRepository $userRepository
    )
    {
        $this->travelRepository = $travelRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @param PublishTravelCommand $command
     * @return Travel
     * @throws \Exception
     */
    public function handle(PublishTravelCommand $command)
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

        return $travel;

    }

}