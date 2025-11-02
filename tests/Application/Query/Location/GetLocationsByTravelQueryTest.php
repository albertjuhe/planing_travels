<?php

namespace App\Tests\Application\Query\Location;

use App\Application\Query\Location\GetLocationsByTravelQuery;
use App\Domain\Travel\Model\Travel;
use PHPUnit\Framework\TestCase;

class GetLocationsByTravelQueryTest extends TestCase
{
    public function testGetLocationsByTravelQueryCreate()
    {
        $travelId = '9c7299d3-665b-4469-ba47-9020c38e91d7';

        $getLocationsByTravelQuery = new GetLocationsByTravelQuery($travelId);
        $this->assertEquals($travelId, $getLocationsByTravelQuery->getTravel());
    }
}
