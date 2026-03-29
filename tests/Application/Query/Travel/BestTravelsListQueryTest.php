<?php

namespace App\Tests\Application\Query\Travel;

use App\Application\Query\Travel\BestTravelsListQuery;
use PHPUnit\Framework\TestCase;

class BestTravelsListQueryTest extends TestCase
{
    public function testGetters(): void
    {
        $query = new BestTravelsListQuery(10, 'stars');

        $this->assertSame(10, $query->getNumberMaxOfTravels());
        $this->assertSame('stars', $query->getOrderedBy());
    }

    public function testDifferentValues(): void
    {
        $query = new BestTravelsListQuery(5, 'watch');

        $this->assertSame(5, $query->getNumberMaxOfTravels());
        $this->assertSame('watch', $query->getOrderedBy());
    }
}
