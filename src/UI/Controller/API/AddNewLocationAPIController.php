<?php

namespace App\UI\Controller\API;

use App\Application\Command\Travel\AddTravelCommand;
use App\Domain\Travel\Model\Travel;
use App\Infrastructure\TravelBundle\Form\TravelType;
use App\UI\Controller\http\BaseController;
use App\Domain\Location\Model\Location;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use App\Domain\User\Exceptions\UserDoesntExists;
use League\Tactician\CommandBus;
use Symfony\Component\HttpFoundation\JsonResponse;

class AddNewLocationAPIController extends BaseController
{

    public function __construct(CommandBus $commandBus)
    {
        parent::__construct($commandBus);
    }

    /**
     * @Route("/api/user/{userId}/location",name="newAPILocation")
     * @Method({"POST"})
     */
    public function newLocation(Request $request, $userId)
    {
        $body = $request->getContent();
        $data = json_decode($body);

        $location = new Location();

        return new Response($body);
    }
}