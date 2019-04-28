<?php
/**
 * Created by PhpStorm.
 * User: albert.juhe
 * Date: 09/10/2018
 * Time: 06:48.
 */

namespace App\Tests\Model;

use App\Domain\Travel\Model\Travel;
use App\Domain\Travel\ValueObject\TravelId;
use App\Domain\TypeLocation\Model\TypeLocation;
use PHPUnit\Framework\TestCase;
use App\Domain\Location\Model\Location;
use App\Domain\Mark\Model\Mark;
use App\Domain\Travel\ValueObject\GeoLocation;

class LocationTest extends TestCase
{
    /** @var Location */
    private $locationId;

    protected function setUp()
    {
        $this->locationId = uniqid();
        $this->location = Location::fromIdAndTitle($this->locationId, 'location1');
        $this->location->setCreatedAt(new \DateTimeImmutable('2018-10-10'));
        $this->location->setUpdatedAt(new \DateTimeImmutable('2018-10-10'));
        $this->location->setSlug('location-slug');
        $this->location->setDescription('location-description');
        $this->location->setUrl('www.url.com');
    }

    public function testConstructors()
    {
        $this->assertEquals('location1', $this->location->getTitle());
        $this->assertEquals($this->locationId, $this->location->getId()->id());

        $location2 = Location::fromIdAndTitle($this->locationId, 'location1');
        $this->assertTrue($location2->equalTo($this->location));
    }

    public function testSettersGetters()
    {
        $now = (new \DateTimeImmutable('2018-10-10'))->getTimestamp();
        $date = $this->location->getCreatedAt()->getTimestamp();
        $this->assertEquals($date, $now);

        $date = $this->location->getUpdatedAt()->getTimestamp();
        $this->assertEquals($date, $now);

        $this->assertEquals($this->location->getSlug(), 'location-slug');

        $this->assertEquals($this->location->getDescription(), 'location-description');

        $this->assertEquals($this->location->getUrl(), 'www.url.com');

        $geoLocation = new GeoLocation(1, 20, 50, 8.0, 1.23401, 9);
        /** @var Mark $mark */
        $mark = Mark::fromGeolocationAndId($geoLocation, 1);
        $this->location->setMark($mark);
        /** @var Mark $markNew */
        $markNew = $this->location->getMark();
        $this->assertTrue($mark->equals($markNew));
    }

    public function testCreateFromArray()
    {
        $data = [
            'comment' => 'comment',
            'link' => 'link',
            'placeAddress' => 'placeAddress',
        ];

        $location = Location::fromArray($data);
        $this->assertEquals($location->getDescription(), $data['comment']);
        $this->assertEquals($location->getUrl(), $data['link']);
        $this->assertEquals($location->getTitle(), $data['placeAddress']);
    }

    public function testToArray()
    {
        $id = uniqid();
        $createdAt = new \DateTime();
        $updatedAt = new \DateTime();
        $title = uniqid();
        $url = uniqid();
        $slug = uniqid();
        $description = uniqid();
        $markId = uniqid();
        $travelid1 = uniqid();
        $typeLocationId = mt_rand();
        $stars = mt_rand();

        $location = Location::fromIdAndTitle($id, $title);
        $location->setTitle($title);
        $location->setUrl($url);
        $location->setSlug($slug);
        $location->setDescription($description);
        $location->setStars($stars);

        $mark = $this->createMock(Mark::class);
        $mark->method('getId')->willReturn($markId);
        $location->setMark($mark);

        $travelId = TravelId::create($travelid1);
        $travel = $this->createMock(Travel::class);
        $travel->method('getId')->willReturn($travelId);
        $location->setTravel($travel);

        $typeLocation = $this->createMock(TypeLocation::class);
        $typeLocation->method('getId')->willReturn($typeLocationId);
        $location->setTypeLocation($typeLocation);

        $location->setCreatedAt($createdAt);
        $location->setUpdatedAt($updatedAt);

        $data = [
            'id' => $id,
            'createdAt' => $createdAt,
            'updatedAt' => $updatedAt,
            'title' => $title,
            'url' => $url,
            'slug' => $slug,
            'description' => $description,
            'mark' => $markId,
            'travel' => $travelid1,
            'typeLocation' => $typeLocationId,
            'stars' => $stars,
        ];
        $this->assertEquals($data, $location->toArray());
    }
}
