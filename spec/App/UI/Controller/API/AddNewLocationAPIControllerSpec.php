<?php

namespace spec\App\UI\Controller\API;

use App\UI\Controller\http\BaseController;
use League\Tactician\CommandBus;
use PhpSpec\ObjectBehavior;
use Symfony\Component\Security\Core\Security;

class AddNewLocationAPIControllerSpec extends ObjectBehavior
{
    public function let(CommandBus $commandBus, Security $security)
    {
        $this->beConstructedWith($commandBus, $security);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(BaseController::class);
    }
}
