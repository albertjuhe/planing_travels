<?php

namespace App\Tests\Domain\User\ValueObject;

use App\Domain\User\ValueObject\UserId;
use PHPUnit\Framework\TestCase;

class UserIdTest extends TestCase
{
    public function testTravelIdCreation()
    {
        $id = mt_rand();
        $userId = UserId::create($id);
        $this->equality($userId, $id);
    }

    public function testTravelIdCreationEmptyId()
    {
        $id = mt_rand();
        $userId = UserId::create($id);
        $this->assertInternalType('int', $userId->id());
        $this->assertInternalType('string', $userId->__toString());
    }

    public function testTravelIdCheckEquality()
    {
        $id = uniqid();
        $userId1 = UserId::create($id);
        $userId2 = UserId::create($id);
        $this->assertTrue($userId1->equalsTo($userId2));
    }

    private function equality($userId, $id)
    {
        $this->assertEquals($userId->id(), $id);
        $this->assertEquals($userId->__toString(), $id);
    }
}
