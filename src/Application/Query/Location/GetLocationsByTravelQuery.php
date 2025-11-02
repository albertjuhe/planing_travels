<?php

namespace App\Application\Query\Location;

class GetLocationsByTravelQuery
{
    private $travel;

    public function __construct(string $travel)
    {
        $this->travel = $travel;
    }

    /**
     * @return mixed
     */
    public function getTravel(): string
    {
        return $this->travel;
    }
}
