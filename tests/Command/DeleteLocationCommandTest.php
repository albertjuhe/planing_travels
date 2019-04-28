<?php

namespace App\Tests\Command;

use App\Application\Command\Location\DeleteLocationCommand;
use App\Domain\User\ValueObject\UserId;
use PHPUnit\Framework\TestCase;

class DeleteLocationCommandTest extends TestCase
{
    public function testDelelocationCommand()
    {
        $locationId = uniqid();
        $travelId = uniqid();
        $userId = $this->createMock(UserId::class);

        $deleteLocationCommand = new DeleteLocationCommand($locationId, $travelId, $userId);
        $this->assertEquals($locationId, $deleteLocationCommand->getLocationId());
        $this->assertEquals($travelId, $deleteLocationCommand->getTravelId());
        $this->assertEquals($userId, $deleteLocationCommand->getUserId());
    }
}
