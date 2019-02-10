<?php


namespace App\Application\Command\Location;


use App\Application\Command\Command;
use App\Domain\Location\Model\Location;
use App\Domain\Mark\Model\Mark;
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
     * @var Mark
     */
    private $mark;

       public function __construct(int $travelId, Location $location, User $user, Mark $mark)
    {
        $this->travelId = $travelId;
        $this->location = $location;
        $this->user = $user;
        $this->mark = $mark;
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

    /**
     * @return Mark
     */
    public function getMark(): Mark
    {
        return $this->mark;
    }



}