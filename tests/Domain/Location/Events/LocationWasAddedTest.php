<?php

namespace App\Tests\Domain\Location\Events;

use App\Domain\Location\Events\LocationWasAdded;
use PHPUnit\Framework\TestCase;

class LocationWasAddedTest extends TestCase
{
    private $locationWasAdded;

    public function setUp()
    {
        $this->locationWasAdded = new LocationWasAdded(
            []
        );
    }

    public function testGetLocation(): void
    {
        $this->assertEquals($this->locationWasAdded->getLocation(), []);
    }

    public function testOcurredOn(): void
    {
        $this->assertInstanceOf(\DateTime::class, $this->locationWasAdded->occurredOn());
    }
}
