<?php

namespace App\Tests\Domain\Travel\Events;

use App\Domain\Travel\Events\TravelWasPublished;
use App\Domain\Travel\Model\Travel;
use App\Domain\User\Model\User;
use PHPUnit\Framework\TestCase;
use App\Domain\Travel\ValueObject\GeoLocation;
use App\Application\DataTransformers\Travel\TravelPublishDataTransformer;

class TravelWasPublishedTest extends TestCase
{
    /** @var User */
    private $user;
    /** @var Travel */
    private $travel;

    public function setUp()
    {
        $this->user = User::byId(1);
        $this->travel = Travel::fromGeoLocation(new GeoLocation(1, 1, 1, 1, 1, 1));
    }

    public function testSettersGetters()
    {
        $travelWasPublished = new TravelWasPublished(
            (new TravelPublishDataTransformer($this->travel))->read(),
            $this->user->getId()->id()
        );
        $this->assertEquals($this->travel->getId()->id(), $travelWasPublished->getTravel()['id']);

        $now = new \DateTime();
        $travelWasPublished->setOccuredOn($now);
        $this->assertEquals($now, $travelWasPublished->occurredOn());
    }
}
