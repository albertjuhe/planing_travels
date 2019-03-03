<?php

namespace spec\App\Application\Command\Location;

use App\Application\Command\Location\DeleteLocationCommand;
use PhpSpec\ObjectBehavior;

class DeleteLocationCommandSpec extends ObjectBehavior
{
    private $locationId;

    public function let()
    {
        $this->locationId = rand();

        $this->beConstructedWith($this->locationId);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(DeleteLocationCommand::class);
    }

    public function it_returns_location_id()
    {
        $this->getLocationId()->shouldReturn($this->locationId);
    }
}
