<?php

namespace App\Tests\Query;

use App\Application\Query\Travel\ShowTravelBySlugQuery;
use PHPUnit\Framework\TestCase;

class ShowTravelBySlugQueryTest extends TestCase
{
    public function testCreateShowTravelBySlugQuery()
    {
        $slug = uniqid();

        $showTravelBySlugQuery = new ShowTravelBySlugQuery($slug);

        $this->assertEquals($slug, $showTravelBySlugQuery->getSlug());
    }
}
