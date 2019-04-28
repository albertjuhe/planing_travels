<?php

namespace App\Tests\Query;

use App\Application\Query\Travel\BestTravelsListQuery;
use PHPUnit\Framework\TestCase;

class BestTravelsListQueryTest extends TestCase
{
    public function testCreateBestTravelsListQuery()
    {
        $numberMaxOfTravels = mt_rand();
        $orderedBy = uniqid();

        $bestTravelsListQuery = new BestTravelsListQuery(
            $numberMaxOfTravels,
            $orderedBy
        );

        $this->assertEquals($numberMaxOfTravels, $bestTravelsListQuery->getNumberMaxOfTravels());
        $this->assertEquals($orderedBy, $bestTravelsListQuery->getOrderedBy());
    }
}
