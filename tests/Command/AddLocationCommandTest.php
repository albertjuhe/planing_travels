<?php

namespace App\Tests\Command;

use App\Application\Command\Location\AddLocationCommand;
use App\Domain\Location\Model\Location;
use App\Domain\Mark\Model\Mark;
use PHPUnit\Framework\TestCase;

class AddLocationCommandTest extends TestCase
{
    public function testAddLocationCommandCreation()
    {
        $location = $this->createMock(Location::class);
        $mark = $this->createMock(Mark::class);
        $travelId = uniqid();
        $userId = mt_rand();
        $locationType = mt_rand();

        $addLocationCommand = new AddLocationCommand(
            $travelId,
            $location,
            $userId,
            $mark,
            $locationType
        );

        $this->assertEquals($travelId, $addLocationCommand->getTravelId());
        $this->assertEquals($location, $addLocationCommand->getLocation());
        $this->assertEquals($userId, $addLocationCommand->getUser());
        $this->assertEquals($mark, $addLocationCommand->getMark());
        $this->assertEquals($locationType, $addLocationCommand->getLocationType());
    }
}
