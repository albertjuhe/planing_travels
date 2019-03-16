<?php

namespace App\UI\Controller\API;

use App\Application\Command\Location\GetLocationsByTravelCommand;
use App\UI\Controller\http\BaseController;
use League\Tactician\CommandBus;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class GetLocationsAPIController extends BaseController
{
    /**
     * @var Security
     */
    private $security;

    public function __construct(CommandBus $commandBus, Security $security)
    {
        parent::__construct($commandBus);
        $this->security = $security;
    }

    /**
     * @Route("api/travel/{travel}/locations", name="locations_by_travel")
     * @Method({"GET"})
     */
    public function getLocationsByTravel(Request $request, int $travel)
    {

        $user = $this->security->getUser();
        if (empty($user)) {
            return new JsonResponse(
                $response['error'] = 'Operation not allowed'
            );
        }


        $getLocationsByTravel = new GetLocationsByTravelCommand($travel);
        $locations = $this->commandBus->handle($getLocationsByTravel);

        return new JsonResponse(
            $response['data'] = [
                'type' => 'travel',
                'id' => $travel,
                'locations' => $locations,
            ]
        );
    }
}
