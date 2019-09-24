<?php

namespace App\Tests\Application\Query;

use App\Application\Query\Travel\GetMyTravelsQuery;
use PHPUnit\Framework\TestCase;

class GetMyTravelsQueryTest extends TestCase
{
    public function testQueryCreation()
    {
        $userId = mt_rand();
        $getMyTravelsQuery = new GetMyTravelsQuery($userId);
        $this->assertEquals($userId, $getMyTravelsQuery->getUser());
    }
}
