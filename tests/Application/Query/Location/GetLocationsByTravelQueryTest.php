<?php

namespace App\Tests\Application\Query\Location;

use App\Application\Query\Location\GetLocationsByTravelQuery;
use PHPUnit\Framework\TestCase;

class GetLocationsByTravelQueryTest extends TestCase
{
    public function testGetLocationsByTravelQueryCreate()
    {
        $travelId = uniqid();
        $getLocationsByTravelQuery = new GetLocationsByTravelQuery($travelId);
        $this->assertEquals($travelId, $getLocationsByTravelQuery->getTravel());
    }
}
