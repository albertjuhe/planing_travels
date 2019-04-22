<?php

namespace App\Application\Query\Travel;

use App\Application\Query\Query;

class BestTravelsListQuery implements Query
{
    /** @var int */
    private $numberMaxOfTravels;
    /** @var string */
    private $orderedBy;

    /**
     * BestTravelsListCommand constructor.
     *
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
     * @return mixed
     */
    public function getOrderedBy()
    {
        return $this->orderedBy;
    }
}
