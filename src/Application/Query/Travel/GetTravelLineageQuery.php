<?php

namespace App\Application\Query\Travel;

use App\Application\Query\Query;

class GetTravelLineageQuery implements Query
{
    private string $travelId;

    public function __construct(string $travelId)
    {
        $this->travelId = $travelId;
    }

    public function getTravelId(): string
    {
        return $this->travelId;
    }
}
