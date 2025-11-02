<?php

namespace App\Tests\Application\Command\Travel;

use App\Application\Command\Travel\AddTravelCommand;
use App\Domain\Travel\Model\Travel;
use App\Domain\User\Model\User;
use PHPUnit\Framework\TestCase;

class AddTravelCommandTest extends TestCase
{
    public function testGettersSetters()
    {
        $user = $this->createMock(User::class);
        $travel = $this->createMock(Travel::class);
        $addCommandTravel = new AddTravelCommand($travel, $user);

        $this->assertEquals($addCommandTravel->getUser(), $user);
        $this->assertEquals($addCommandTravel->getTravel(), $travel);
    }
}
