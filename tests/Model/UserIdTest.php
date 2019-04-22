<?php

namespace App\Tests\Model;

use PHPUnit\Framework\TestCase;
use App\Domain\User\ValueObject\UserId;

class UserIdTest extends TestCAse
{
    public function testId()
    {
        $userId = new UserId(12);
        $this->assertEquals(12, $userId->id());
    }

    public function testEqualsTo() {
        $id = mt_rand();
        $userId = new UserId($id);
        $userId2 = new UserId($id);
        $this->assertTrue($userId->equalsTo($userId2));
    }

    public function testNotEqualsTo() {
        $id = mt_rand();
        $userId = new UserId($id);
        $userId2 = new UserId($id +1);
        $this->assertFalse($userId->equalsTo($userId2));
    }
}
