<?php

namespace App\Tests\ValueObject;

use App\Domain\Travel\ValueObject\TravelId;
use PHPUnit\Framework\TestCase;

class TravelIdTest extends TestCase
{
    public function testTravelIdCreation()
    {
        $id = uniqid();
        $travelId = TravelId::create($id);
        $this->equality($travelId, $id);
    }

    public function testTravelIdCreationEmptyId()
    {
        $id = uniqid();
        $travelId = TravelId::create($id);
        $this->assertInternalType('string', $travelId->id());
        $this->assertInternalType('string', $travelId->__toString());
    }

    public function testTravelIdCheckEquality()
    {
        $id = uniqid();
        $travelId1 = TravelId::create($id);
        $travelId2 = TravelId::create($id);
        $this->assertTrue($travelId1->equalsTo($travelId2));
    }

    private function equality($travelId, $id)
    {
        $this->assertEquals($travelId->id(), $id);
        $this->assertEquals($travelId->__toString(), $id);
    }
}
