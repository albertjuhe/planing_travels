<?php

namespace App\Tests\Application\DataTransformers;

use PHPUnit\Framework\TestCase;
use App\Application\DataTransformers\Travel\TravelPublishDataTransformer;
use App\Domain\Travel\Model\Travel;
use App\Domain\User\Model\User;
use App\Domain\Travel\ValueObject\GeoLocation;

class TravelPublishDataTransformerTest extends TestCase
{
    private $travel;

    public function setUp()
    {
        $this->travel = Travel::fromTitleAndGeolocationAndUser(
            'Dummy1',
            new GeoLocation(1, 2, 3, 4, 5, 6),
            User::byId(1)
        );
        $this->travel->setPublishedAt(new \DateTime('2018-01-01'));
    }

    public function testRead()
    {
        $id = $this->travel->getId()->id();
        $travelPublishDataTransformer = new TravelPublishDataTransformer($this->travel);
        $this->assertEquals(
            $travelPublishDataTransformer->read(),
            [
                'id' => $id,
                'publishedAt' => new \DateTime('2018-01-01'),
                'status' => Travel::TRAVEL_DRAFT,
            ]
        );
    }
}
