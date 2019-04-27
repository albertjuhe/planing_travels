<?php

namespace App\Application\Query\Location;

use App\Domain\Travel\Model\Travel;

class GetLocationsByTravelQuery
{
    private $travel;

    public function __construct(Travel $travel)
    {
        $this->travel = $travel;
    }

    /**
     * @return mixed
     */
    public function getTravel(): Travel
    {
        return $this->travel;
    }
}
