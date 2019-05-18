<?php

namespace App\Tests\Query;

use App\Application\Query\Travel\GetMyTravelsQuery;
use App\Domain\User\Model\User;
use PHPUnit\Framework\TestCase;

class GetMyTravelsQueryTest extends TestCase
{
    public function testQueryCreation()
    {
        $user = $this->createMock(User::class);
        $getMyTravelsQuery = new GetMyTravelsQuery($user);
        $this->assertEquals($user, $getMyTravelsQuery->getUser());
    }
}
