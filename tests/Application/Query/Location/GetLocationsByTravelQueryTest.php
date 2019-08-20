<?php

namespace App\Tests\Application\Query\Location;

use App\Application\Query\Location\GetLocationsByTravelQuery;
use App\Domain\Travel\Model\Travel;
use PHPUnit\Framework\TestCase;

class GetLocationsByTravelQueryTest extends TestCase
{
    public function testGetLocationsByTravelQueryCreate()
    {
        $travel = $this->createMock(Travel::class);
        $getLocationsByTravelQuery = new GetLocationsByTravelQuery($travel);
        $this->assertEquals($travel, $getLocationsByTravelQuery->getTravel());
    }
}
