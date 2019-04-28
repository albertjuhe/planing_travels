<?php

namespace App\Tests\Command;

use App\Application\Command\Travel\UpdateTravelCommand;
use App\Domain\Travel\Model\Travel;
use App\Domain\User\Model\User;
use PHPUnit\Framework\TestCase;

class UpdateTravelCommandTest extends TestCase
{
    public function testUpdateTravelCommandCreation()
    {
        $user = $this->createMock(User::class);
        $travel = $this->createMock(Travel::class);

        $updateTravelCommand = new UpdateTravelCommand($travel, $user);

        $this->assertEquals($user, $updateTravelCommand->user());
        $this->assertEquals($travel, $updateTravelCommand->travel());
    }
}
