<?php


namespace App\Tests\Model;

use PHPUnit\Framework\TestCase;
use App\Domain\Travel\Model\Travel;
use App\Domain\Travel\ValueObject\GeoLocation;
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

        $this->assertTrue($user->equalsTo($newUser));

        $user = User::fromId(2);
        $this->assertFalse($user->equalsTo($newUser));
    }

    public function testFromGeoLocation() {
        $geoLocation = new GeoLocation(10,20,30,40,50,60);
        $travel = Travel::fromGeoLocation($geoLocation);

        $geoLocation2 = new GeoLocation(10,20,30,40,50,60);
        $this->assertTrue($geoLocation2->equal($travel->getGeoLocation()));
    }
}