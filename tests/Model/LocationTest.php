<?php
/**
 * Created by PhpStorm.
 * User: albert.juhe
 * Date: 09/10/2018
 * Time: 06:48
 */
namespace App\Tests\Model;

use PHPUnit\Framework\TestCase;
use App\Domain\Location\Model\Location;

class LocationTest  extends TestCase {

    public function testConstructors() {
        $location = Location::fromIdAndTitle(1,'location1');
        $this->assertEquals('location1',$location->getTitle());
        $this->assertEquals(1,$location->getId());

        $location2 = Location::fromIdAndTitle(1,'location1');
        $this->assertTrue($location2->equals($location));

    }

}