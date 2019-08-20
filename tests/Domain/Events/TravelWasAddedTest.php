<?php
/**
 * Created by PhpStorm.
 * User: albert.juhe
 * Date: 09/11/2018
 * Time: 17:09.
 */

namespace App\Tests\Domain\Events;

use App\Domain\Travel\Events\TravelWasAdded;
use PHPUnit\Framework\TestCase;

class TravelWasAddedTest extends TestCase
{
    public function testCreateEvent()
    {
        $travel = [];

        $travelWasAdded = new TravelWasAdded($travel);

        $now = new \DateTime();
        $travelWasAdded->setOccuredOn($now);

        $this->assertEquals($now, $travelWasAdded->occurredOn());
    }

    public function testGetTravelFromEvent()
    {
        $travel = ['id' => uniqid()];

        $travelWasAdded = new TravelWasAdded($travel);
        $this->assertEquals($travel, $travelWasAdded->getTravel());
    }
}
