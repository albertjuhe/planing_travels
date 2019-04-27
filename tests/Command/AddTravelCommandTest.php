<?php
/**
 * Created by PhpStorm.
 * User: albert.juhe
 * Date: 06/11/2018
 * Time: 18:57.
 */

namespace App\Tests\Command;

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
