<?php


namespace App\Tests\Model;

use PHPUnit\Framework\TestCase;
use App\Domain\Travel\Model\Travel;
use App\Domain\User\Model\User;

class TravelTest extends TestCase
{
    public function testTravelCreationStarsAndWatchInitialValue()
    {
        $travel = new Travel();
        $this->assertEquals($travel->getStarts(),0);
        $this->assertEquals($travel->getWatch(),0);
    }

    public function testFromUSer() {
        $user = User::fromId(1);
        $travel = Travel::fromUser($user);
        $newUser = $travel->getUser();

        $this->assertTrue($user->equal($newUser));

        $user = User::fromId(2);
        $this->assertFalse($user->equal($newUser));
    }
}