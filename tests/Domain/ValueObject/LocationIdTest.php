<?php

namespace App\Tests\Domain\ValueObject;

use App\Domain\Location\ValueObject\LocationId;
use PHPUnit\Framework\TestCase;

class LocationIdTest extends TestCase
{
    public function testTravelIdCreation()
    {
        $id = uniqid();
        $locationId = LocationId::create($id);
        $this->equality($locationId, $id);
    }

    public function testTravelIdCreationEmptyId()
    {
        $id = uniqid();
        $locationId = LocationId::create($id);
        $this->assertInternalType('string', $locationId->id());
        $this->assertInternalType('string', $locationId->__toString());
    }

    public function testTravelIdCheckEquality()
    {
        $id = uniqid();
        $locationId1 = LocationId::create($id);
        $locationId2 = LocationId::create($id);
        $this->assertTrue($locationId1->equalsTo($locationId2));
    }

    private function equality($locationId, $id)
    {
        $this->assertEquals($locationId->id(), $id);
        $this->assertEquals($locationId->__toString(), $id);
    }
}
