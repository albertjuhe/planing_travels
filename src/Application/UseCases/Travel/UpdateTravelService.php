<?php
/**
 * Created by PhpStorm.
 * User: albert.juhe
 * Date: 03/10/2018
 * Time: 07:17
 */

namespace App\Application\UseCases\Travel;

use App\Domain\Event\DomainEventPublisher;
use App\Domain\Travel\Events\TravelWasUpdated;
use App\Domain\Travel\Exceptions\InvalidTravelUser;
use App\Domain\User\Model\User;
use App\Domain\Travel\Repository\TravelRepository;
use App\Domain\Travel\Model\Travel;
use App\Application\Command\Travel\UpdateTravelCommand;

class UpdateTravelService
{
    /**
     * @var TravelRepository;
     */
    private $travelRepository;

    /**
     * AddTravelService constructor.
     * @param TravelRepository $travelRepository
     */
    public function __construct(TravelRepository $travelRepository)
    {
        $this->travelRepository = $travelRepository;
    }

    /**
     * Modify a travel
     * @param UpdateTravelCommand $commnand
     * @throws InvalidTravelUser
     */
    public function handle(UpdateTravelCommand $commnand) {
        /** @var Travel */
        $travel = $commnand->travel();
        /** @var User */
        $user = $commnand->user();

        //Only the owner can modify the travel
        if (!$travel->getUser()->getUserId()==$user->getUserId())
            throw new InvalidTravelUser();

        DomainEventPublisher::instance()->publish(new TravelWasUpdated($travel));
        $this->travelRepository->save($travel);
    }

}

