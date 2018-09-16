<?php


namespace App\Tests\Model;

use PHPUnit\Framework\TestCase;
use App\Domain\Travel\Model\Travel;

class TravelTest extends TestCase
{
    public function testTravelCreationStarsAndWatchInitialValue()
    {
        $travel = new Travel();
        $this->assertEquals($travel->getStarts(),0);
        $this->assertEquals($travel->getWatch(),0);
    }
}