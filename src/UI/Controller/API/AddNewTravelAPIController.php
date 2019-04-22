<?php

namespace App\UI\Controller\API;

use App\Domain\Travel\Model\Travel;
use App\UI\Controller\http\CommandController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use League\Tactician\CommandBus;
use Symfony\Component\HttpFoundation\JsonResponse;

class AddNewTravelAPIController extends CommandController
{
    public function __construct(CommandBus $commandBus)
    {
        parent::__construct($commandBus);
    }

    /**
     * @Route("/api/user/{userId}/travel",name="newAPITravel")
     * @Method({"POST"})
     */
    public function newTravel($userId)
    {
        return new JsonResponse(['data' => $userId]);
    }
}
