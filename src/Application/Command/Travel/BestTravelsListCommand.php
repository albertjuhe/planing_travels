<?php


namespace App\Application\Command\Travel;

class BestTravelsListCommand
{
    /** @var int */
    private $numberMaxOfTravels;
    /** @var string */
    private $orderedBy;

    /**
     * BestTravelsListCommand constructor.
     * @param $numberMaxOfTravels
     * @param $orderedBy
     */
    public function __construct($numberMaxOfTravels, $orderedBy)
    {
        $this->numberMaxOfTravels = $numberMaxOfTravels;
        $this->orderedBy = $orderedBy;
    }

    /**
     * @return mixed
     */
    public function getNumberMaxOfTravels()
    {
        return $this->numberMaxOfTravels;
    }

    /**
     * @param mixed $numberMaxOfTravels
     */
    public function setNumberMaxOfTravels($numberMaxOfTravels): void
    {
        $this->numberMaxOfTravels = $numberMaxOfTravels;
    }

    /**
     * @return mixed
     */
    public function getOrderedBy()
    {
        return $this->orderedBy;
    }

    /**
     * @param mixed $orderedBy
     */
    public function setOrderedBy($orderedBy): void
    {
        $this->orderedBy = $orderedBy;
    }


}