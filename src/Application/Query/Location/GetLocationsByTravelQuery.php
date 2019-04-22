<?php

namespace App\Application\Query\Location;

class GetLocationsByTravelQuery
{
    private $travel;

    public function __construct($travel)
    {
        $this->travel = $travel;
    }

    /**
     * @return mixed
     */
    public function getTravel()
    {
        return $this->travel;
    }
}
