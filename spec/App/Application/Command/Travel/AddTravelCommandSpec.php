<?php

namespace spec\App\Application\Command\Travel;

use App\Application\Command\Command;
use App\Domain\Travel\Model\Travel;
use App\Domain\User\Model\User;
use PhpSpec\ObjectBehavior;

class AddTravelCommandSpec extends ObjectBehavior
{
    public function let(Travel $travel, User $user)
    {
        $this->beConstructedWith($travel, $user);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(Command::class);
    }

    public function it_gets_travel(Travel $travel)
    {
        $this->getTravel()->shouldReturn($travel);
    }

    public function it_gets_User(User $user)
    {
        $this->getUser()->shouldReturn($user);
    }
}
