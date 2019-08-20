<?php
/**
 * Created by PhpStorm.
 * User: albert.juhe
 * Date: 18/10/2018
 * Time: 07:22.
 */

namespace App\Tests\Domain\Model;

use App\Domain\Mark\Model\Mark;
use PHPUnit\Framework\TestCase;
use App\Domain\Travel\ValueObject\GeoLocation;
use App\Domain\Location\Model\Location;

class MarkTest extends TestCase
{
    /** @var Mark */
    private $mark;

    public function setUp()
    {
        $this->mark = new Mark();
        $this->mark->setId(89);
        $this->mark->setCreatedAt(new \DateTimeImmutable('2018-10-10'));
        $this->mark->setUpdatedAt(new \DateTimeImmutable('2018-10-10'));
        $geoLocation = new GeoLocation(1, 20, 50, 8.0, 1.23401, 9);
        $this->mark->setGeoLocation($geoLocation);
        $this->mark->setTitle('example-mark');
        $json = "{info: 'json'}";
        $this->mark->setJson($json);
    }

    public function testCreateFromeGeolocationAndId()
    {
        $geoLocation = new GeoLocation(1, 20, 50, 8.0, 1.23401, 9);
        /** @var Mark $mark */
        $mark = Mark::fromGeolocationAndId($geoLocation, 1);

        $this->assertEquals($mark->getId(), 1);
        $this->assertTrue($mark->getGeoLocation()->equal($geoLocation));
    }

    public function testSettersGetters()
    {
        $now = (new \DateTimeImmutable('2018-10-10'))->getTimestamp();
        $date = $this->mark->getCreatedAt()->getTimestamp();
        $this->assertEquals($date, $now);

        $date = $this->mark->getUpdatedAt()->getTimestamp();
        $this->assertEquals($date, $now);

        $this->assertEquals($this->mark->getTitle(), 'example-mark');

        $this->assertEquals($this->mark->getJson(), "{info: 'json'}");

        $geoLocation = new GeoLocation(1, 20, 50, 8.0, 1.23401, 9);
        $this->assertTrue($this->mark->getGeoLocation()->equal($geoLocation));
    }

    public function testEquals()
    {
        $geoLocation = new GeoLocation(1, 20, 50, 8.0, 1.23401, 9);
        $mark = Mark::fromGeolocationAndId($geoLocation, 89);
        $this->assertTrue($this->mark->equals($mark));
    }

    public function testAddLocation()
    {
        $location1 = Location::fromIdAndTitle(1, 'location1');
        $location2 = Location::fromIdAndTitle(2, 'location2');

        $this->mark->addLocation($location1);
        $this->mark->addLocation($location2);

        $this->assertCount(2, $this->mark->getLocation());

        $this->mark->removeLocation($location1);
        $this->assertCount(1, $this->mark->getLocation());
    }

    public function testToString()
    {
        $this->assertEquals($this->mark->__toString(), 'example-mark');
    }
}
