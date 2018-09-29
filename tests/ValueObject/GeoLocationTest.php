<?php


namespace App\Tests\ValueObject;

use App\Domain\Travel\ValueObject\GeoLocation;
use PHPUnit\Framework\TestCase;

class GeoLocationTest extends TestCase
{
    public function testEqual()
    {
        $geoLocation = new GeoLocation(10, 20, 30, 40, 50, 60);
        $geoLocation2 = new GeoLocation(10, 20, 30, 40, 50, 60);
        $this->assertTrue($geoLocation->equal($geoLocation2));

        $geoLocation3 = new GeoLocation(1, 2, 30, 40, 50, 60);
        $this->assertFalse($geoLocation->equal($geoLocation3));

    }
}