<?php

namespace App\Tests\Application\Command\Travel;

use App\Application\Command\Travel\PublishTravelCommand;
use App\Domain\User\Model\User;
use PHPUnit\Framework\TestCase;

class PublishTravelCommandTest extends TestCase
{
    public function testGetterSetters()
    {
        $user = User::byId(1);
        $publishTravelCommand = new PublishTravelCommand('slug', $user);

        $this->assertEquals($publishTravelCommand->getTravelSlug(), 'slug');

        $this->assertEquals($publishTravelCommand->getUser()->userId()->id(), 1);

        $publishTravelCommand->setTravelSlug('travel-slug');
        $this->assertEquals($publishTravelCommand->getTravelSlug(), 'travel-slug');

        $user1 = User::byId(2);
        $publishTravelCommand->setUser($user1);
        $this->assertEquals($publishTravelCommand->getUser()->userId()->id(), 2);
    }
}
