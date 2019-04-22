<?php
/**
 * Created by PhpStorm.
 * User: albert.juhe
 * Date: 09/11/2018
 * Time: 17:09.
 */

namespace App\Tests\Events;

use App\Domain\Travel\Events\TravelWasAdded;
use App\Domain\Travel\Model\Travel;
use App\Domain\Travel\ValueObject\GeoLocation;
use App\Domain\User\Model\User;
use PHPUnit\Framework\TestCase;

class TravelWasAddedTest extends TestCase
{
    /** @var Travel */
    private $travel;

    public function setUp()
    {
        /** @var User $user */
        $user = User::byId(1);
        $user->setUsername('username');
        $this->travel = Travel::fromGeoLocation(new GeoLocation(1, 1, 1, 1, 1, 1));
        $this->travel->setUser($user);
    }

    public function testSettersGetters()
    {
        $travelWasAdded = new TravelWasAdded($this->travel->toArray());

        $now = new \DateTime();
        $travelWasAdded->setOccuredOn($now);

        $this->assertEquals($now, $travelWasAdded->occurredOn());
    }
}
