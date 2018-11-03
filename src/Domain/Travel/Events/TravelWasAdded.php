<?php
/**
 * Created by PhpStorm.
 * User: albert.juhe
 * Date: 24/10/2018
 * Time: 07:47
 */

namespace App\Domain\Travel\Events;


use Symfony\Component\EventDispatcher\Event;
use App\Domain\Travel\Model\Travel;
use App\Domain\User\Model\User;

class TravelWasAdded extends Event
{
    const ADD_TRAVEL_EVENT_REQUEST = 'add_travel_request_event';

    /** @var Travel */
    private $travel;
    /** @var User */
    private $user;
    /** @var \DateTime */
    private $occuredOn;

    /**
     * travelWasAdded constructor.
     * @param Travel $travel
     * @param User $user
     * @throws \Exception
     */
    public function __construct(Travel $travel, User $user)
    {
        $this->travel = $travel;
        $this->user = $user;
        $this->occuredOn = (new \DateTimeImmutable())->getTimestamp();
    }

    /**
     * @return Travel
     */
    public function getTravel(): Travel
    {
        return $this->travel;
    }

    /**
     * @param Travel $travel
     */
    public function setTravel(Travel $travel): void
    {
        $this->travel = $travel;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    /**
     * @return \DateTime
     */
    public function getOccuredOn(): \DateTime
    {
        return $this->occuredOn;
    }

    /**
     * @param \DateTime $occuredOn
     */
    public function setOccuredOn(\DateTime $occuredOn): void
    {
        $this->occuredOn = $occuredOn;
    }


}