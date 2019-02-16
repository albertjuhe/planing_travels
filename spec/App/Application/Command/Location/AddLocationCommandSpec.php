<?php

namespace spec\App\Application\Command\Location;

use App\Application\Command\Location\AddLocationCommand;
use App\Domain\Location\Model\Location;
use App\Domain\Mark\Model\Mark;
use PhpSpec\ObjectBehavior;

class AddLocationCommandSpec extends ObjectBehavior
{
    private $travelId;

    private $user;

    private $locationType;

    public function let(Mark $mark, Location $location)
    {
        $this->travelId = 45;
        $this->user = rand();
        $this->locationType = rand();

        $this->beConstructedWith($this->travelId, $location, $this->user, $mark, $this->locationType);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(AddLocationCommand::class);
    }

    public function it_returns_travel_id()
    {
        $this->getTravelId()->shouldReturn($this->travelId);
    }

    public function it_returns_location(Location $location)
    {
        $this->getLocation()->shouldReturn($location);
    }

    public function it_returns_user()
    {
        $this->getUser()->shouldReturn($this->user);
    }
}
