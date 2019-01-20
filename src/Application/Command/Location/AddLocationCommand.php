<?php


namespace App\Application\Command\Location;


use App\Application\Command\Command;
use App\Domain\Location\Model\Location;
use App\Domain\User\Model\User;

class AddLocationCommand extends Command
{

    /**
     * @var int
     */
    private $travelId;

    /**
     * @var Location
     */
    private $location;

    /**
     * @var User
     */
    private $user;

    /**
     * AddLocationCommand constructor.
     * @param int $travelId
     * @param Location $location
     * @param User $user
     */
    public function __construct(int $travelId, Location $location, User $user)
    {
        $this->travelId = $travelId;
        $this->location = $location;
        $this->user = $user;
    }

    /**
     * @return int
     */
    public function getTravelId(): int
    {
        return $this->travelId;
    }

    /**
     * @return Location
     */
    public function getLocation(): Location
    {
        return $this->location;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }



}