<?php

namespace App\Tests\Model;

use PHPUnit\Framework\TestCase;
use App\Domain\Travel\Model\Travel;
use App\Domain\Travel\ValueObject\GeoLocation;
use App\Domain\User\Model\User;
use App\Domain\Location\Model\Location;
use App\Domain\Gpx\Model\Gpx;

class TravelTest extends TestCase
{
    public function testTravelCreationStarsAndWatchInitialValue()
    {
        $travel = new Travel();
        $this->assertEquals($travel->getStarts(), 0);
        $this->assertEquals($travel->getWatch(), 0);
    }

    public function testFromUSer()
    {
        $user = User::fromId(1);
        $travel = Travel::fromUser($user);
        $newUser = $travel->getUser();

        $this->assertTrue($user->equalsTo($newUser));

        $user = User::fromId(2);
        $this->assertFalse($user->equalsTo($newUser));
    }

    public function testFromGeoLocation()
    {
        $geoLocation = new GeoLocation(10, 20, 30, 40, 50, 60);
        $travel = Travel::fromGeoLocation($geoLocation);

        $geoLocation2 = new GeoLocation(10, 20, 30, 40, 50, 60);
        $this->assertTrue($geoLocation2->equal($travel->getGeoLocation()));
    }

    public function testSettersGetters()
    {
        $geoLocation = new GeoLocation(10, 20, 30, 40, 50, 60);
        $user = User::fromId(2);

        $travel = Travel::fromGeoLocation($geoLocation);
        $travel->setUser($user);

        $travel->setCreatedAt(new \DateTime('2018-01-01'));
        $this->assertEquals(new \DateTime('2018-01-01'), $travel->getCreatedAt());

        $travel->setUpdatedAt(new \DateTime('2018-01-01'));
        $this->assertEquals(new \DateTime('2018-01-01'), $travel->getUpdatedAt());

        $travel->setEndAt(new \DateTime('2018-01-01'));
        $this->assertEquals(new \DateTime('2018-01-01'), $travel->getEndAt());

        $travel->setStartAt(new \DateTime('2018-01-01'));
        $this->assertEquals(new \DateTime('2018-01-01'), $travel->getStartAt());

        $travel->setPublishedAt(new \DateTime('2018-01-01'));
        $this->assertEquals(new \DateTime('2018-01-01'), $travel->getPublishedAt());

        $travel->setTitle('title');
        $this->assertEquals('title', $travel->getTitle());

        $travel->setDescription('description');
        $this->assertEquals('description', $travel->getDescription());

        $travel->setSlug('travel-slug');
        $this->assertEquals('travel-slug', $travel->getSlug());

        $travel->setPhoto('photo.png');
        $this->assertEquals('photo.png', $travel->getPhoto());

        $location = Location::fromIdAndTitle(1, 'Barcelona');

        $travel->setLocation($location);
        $this->assertTrue($travel->getLocation()->equals($location));

        $gpx = new Gpx();
        $gpx->setId(1);
        $travel->setGpx($gpx);
        $gpx2 = $travel->getGpx();
        $this->assertTrue($gpx->equals($gpx2));
    }

    public function testPublishTravel()
    {
        $user = User::byId(1);
        $travel = Travel::fromUser($user);

        $this->assertEquals($travel->getStatus(), Travel::TRAVEL_DRAFT);
        $travel->publish();
        $this->assertEquals($travel->getStatus(), Travel::TRAVEL_PUBLISHED);
    }
}
