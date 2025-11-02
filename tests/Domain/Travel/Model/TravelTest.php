<?php

namespace App\Tests\Domain\Travel\Model;

use App\Domain\Travel\Events\TravelWasPublished;
use App\Tests\Domain\Travel\ValueObject\GeoLocationStub;
use PHPUnit\Framework\TestCase;
use App\Domain\Travel\Model\Travel;
use App\Domain\User\Model\User;
use App\Domain\Location\Model\Location;
use App\Domain\Gpx\Model\Gpx;

class TravelTest extends TestCase
{
    public function testTravelCreationStarsAndWatchInitialValue()
    {
        $travel = new Travel();
        $this->assertEquals($travel->getStars(), 0);
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
        $geoLocation = GeoLocationStub::withLongitudAndLatitude(10, 20);
        $travel = Travel::fromGeoLocation($geoLocation);

        $geoLocation2 = GeoLocationStub::withLongitudAndLatitude(10, 20);
        $this->assertTrue($geoLocation2->equal($travel->getGeoLocation()));
    }

    public function testSettersGetters()
    {
        $userId = mt_rand();
        $geoLocation = GeoLocationStub::random();
        $user = User::fromId($userId);

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
        $this->assertTrue($travel->getLocation()->equalTo($location));

        $gpx = new Gpx();
        $gpx->setId(1);
        $travel->setGpx($gpx);
        $gpx2 = $travel->getGpx();
        $this->assertTrue($gpx->equals($gpx2));

        $stars = mt_rand();
        $travel->setStars($stars);

        $watch = mt_rand();
        $travel->setWatch($watch);

        $this->assertEquals(
            $travel->toArray(),
            [
                'id' => $travel->getId()->id(),
                'title' => 'title',
                'description' => 'description',
                'createdAt' => new \DateTime('2018-01-01'),
                'updatedAt' => new \DateTime('2018-01-01'),
                'slug' => 'travel-slug',
                'latitude' => $geoLocation->lat(),
                'longitud' => $geoLocation->lng(),
                'startAt' => new \DateTime('2018-01-01'),
                'endAt' => new \DateTime('2018-01-01'),
                'userId' => $userId,
                'username' => null,
                'publishedAt' => new \DateTime('2018-01-01'),
                'status' => Travel::TRAVEL_DRAFT,
                'stars' => $stars,
                'watch' => $watch,
            ]
        );
    }

    public function testGetLatitude(): void
    {
        $lat = 10;
        $lng = 20;

        $user = User::byId(1);
        $geoLocation = GeoLocationStub::withLongitudAndLatitude($lat, $lng);
        $travel = Travel::fromGeoLocation($geoLocation);
        $travel->setUser($user);
        $this->assertEquals($lng, $travel->getLongitude());
        $this->assertEquals($lat, $travel->getLatitude());
    }

    public function testPublishTravel(): void
    {
        $user = User::byId(1);
        $travel = Travel::fromUser($user);

        $this->assertEquals(Travel::TRAVEL_DRAFT, $travel->getStatus());
        $travel->publish();
        $this->assertEquals(Travel::TRAVEL_PUBLISHED, $travel->getStatus());
        $events = $travel->pullDomainEvents();

        $this->assertInstanceOf(TravelWasPublished::class, $events[0]);
    }
}
