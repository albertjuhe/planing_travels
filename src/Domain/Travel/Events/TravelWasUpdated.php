<?php
/**
 * Created by PhpStorm.
 * User: albert.juhe
 * Date: 24/10/2018
 * Time: 07:47
 */

namespace App\Domain\Travel\Events;


use App\Domain\Event\DomainEvent;
use App\Domain\Travel\Model\Travel;

class TravelWasUpdated implements DomainEvent
{
    const ADD_TRAVEL_EVENT_REQUEST = 'add_travel_request_event';

    /** @var Travel */
    private $travel;
    /** @var \DateTime */
    private $occuredOn;

    /**
     * travelWasAdded constructor.
     * @param Travel $travel
     * @param User $user
     * @throws \Exception
     */
    public function __construct(Travel $travel)
    {
        $this->travel = $travel;
        $this->occuredOn = new \DateTime();
    }

    /**
     * @return \DateTime
     */
    public function occurredOn()
    {
        return $this->occuredOn;
    }


    /**
     * @return Travel
     */
    public function getTravel(): Travel
    {
        return $this->travel;
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