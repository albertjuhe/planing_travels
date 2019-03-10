<?php

namespace App\Application\Command\Location;

class GetLocationsByTravelCommand
{
    private $travel;

    /**
     * GetLocationsByTravelCommand constructor.
     *
     * @param $travel
     */
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
