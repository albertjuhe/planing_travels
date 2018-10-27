<?php


namespace App\Tests\Model;

use PHPUnit\Framework\TestCase;
use App\Domain\User\ValueObject\UserId;

class UserIdTest extends TestCAse
{
    public function testId()
    {
        $userId = new UserId(12);
        $this->assertEquals(12,$userId->id());
    }
}