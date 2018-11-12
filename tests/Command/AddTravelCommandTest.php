<?php
/**
 * Created by PhpStorm.
 * User: albert.juhe
 * Date: 06/11/2018
 * Time: 18:57
 */

namespace App\Tests\Command;

use App\Application\Command\Travel\AddTravelCommand;
use App\Domain\Travel\Model\Travel;
use App\Domain\Travel\ValueObject\GeoLocation;
use App\Domain\User\Model\User;
use PHPUnit\Framework\TestCase;

class AddTravelCommandTest extends TestCase
{

    public function testGettersSetters() {
        $user = User::byId(1);
        $travel = Travel::fromGeoLocation( new GeoLocation(1,1,1,1,1,1));
        $travel->setId(45);
        $addCommandTravel = new AddTravelCommand($travel,$user);

        $this->assertEquals($addCommandTravel->getUser()->userId(),1);

        $this->assertEquals($addCommandTravel->getTravel()->getId(),45);

        $user1 = User::byId(2);
        $travel1 = Travel::fromGeoLocation( new GeoLocation(1,1,1,1,1,1));
        $travel1->setId(80);
        $addCommandTravel->setUser($user1);
        $addCommandTravel->setTravel($travel1);

        $this->assertEquals($addCommandTravel->getUser()->userId(),2);

        $this->assertEquals($addCommandTravel->getTravel()->getId(),80);


    }
}