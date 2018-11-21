<?php
/**
 * Created by PhpStorm.
 * User: albert.juhe
 * Date: 09/11/2018
 * Time: 17:07
 */

namespace App\Tests\Events;

use App\Domain\Travel\Model\Travel;
use App\Domain\Travel\Events\TravelWasUpdated;
use App\Domain\Travel\ValueObject\GeoLocation;
use PHPUnit\Framework\TestCase;

class TravelWasUpdatedTest extends TestCase
{
    /** @var Travel */
    private $travel;

    public function setUp() {
        $this->travel = Travel::fromGeoLocation( new GeoLocation(1,1,1,1,1,1));
        $this->travel->setId(45);
    }

    public function testSettersGetters() {
        $travelWasPublished = new TravelWasUpdated($this->travel);
        $this->assertEquals($this->travel->getId(), $travelWasPublished->getTravel()->getId());

        $now = new \DateTime();
        $travelWasPublished->setOccuredOn($now);
        $this->assertEquals($now, $travelWasPublished->occurredOn());
    }
}