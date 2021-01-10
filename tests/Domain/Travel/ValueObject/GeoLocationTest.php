<?php

namespace App\Tests\Domain\Travel\ValueObject;

use App\Domain\Travel\ValueObject\GeoLocation;
use PHPUnit\Framework\TestCase;

class GeoLocationTest extends TestCase
{
    public function testEqual()
    {
        $geoLocation = GeoLocationStub::withLongitudAndLatitude(10, 20);
        $geoLocation2 = GeoLocationStub::withLongitudAndLatitude(10, 20);
        $this->assertTrue($geoLocation->equal($geoLocation2));

        $geoLocation3 = GeoLocationStub::random();
        $this->assertFalse($geoLocation->equal($geoLocation3));
    }

    public function testSetGeolocationValues()
    {
        $geoLocation = GeoLocationStub::random();
        $geoLocation->setLat(45.99999999);
        $geoLocation->setLng(0);
        $geoLocation->setLat0(23.4352452);
        $geoLocation->setLng0(5.9892);
        $geoLocation->setLat1(1.43423423);
        $geoLocation->setLng1(1.233);

        $geoLocation1 = new GeoLocation(45.99999999, 0, 23.4352452, 5.9892, 1.43423423, 1.233);
        $this->assertTrue($geoLocation1->equal($geoLocation));
    }
}
