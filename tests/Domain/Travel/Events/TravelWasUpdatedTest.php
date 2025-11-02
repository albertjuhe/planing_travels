<?php

namespace App\Tests\Domain\Travel\Events;

use App\Domain\Travel\Model\Travel;
use App\Domain\Travel\Events\TravelWasUpdated;
use App\Domain\Travel\ValueObject\GeoLocation;
use App\Application\DataTransformers\Travel\TravelPublishDataTransformer;
use PHPUnit\Framework\TestCase;

class TravelWasUpdatedTest extends TestCase
{
    /** @var Travel */
    private $travel;

    public function setUp()
    {
        $this->travel = Travel::fromGeoLocation(new GeoLocation(1, 1, 1, 1, 1, 1));
    }

    public function testSettersGetters()
    {
        $travelWasPublished = new TravelWasUpdated((new TravelPublishDataTransformer($this->travel))->read());
        $this->assertEquals($this->travel->getId()->id(), $travelWasPublished->getTravel()['id']);

        $now = new \DateTime();
        $travelWasPublished->setOccuredOn($now);
        $this->assertEquals($now, $travelWasPublished->occurredOn());
    }
}
