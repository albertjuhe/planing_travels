<?php

namespace App\UI\Controller\API;

use App\Application\Query\Location\GetLocationsByTravelQuery;
use App\Infrastructure\Application\QueryBus\QueryBus;
use App\UI\Controller\http\QueryController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Routing\Attribute\Route;

class GetLocationsAPIController extends QueryController
{
    public function __construct(QueryBus $queryBus, Security $security)
    {
        parent::__construct($queryBus, $security);
    }

    #[Route('api/travel/{travel}/locations', name: 'locations_by_travel', methods: ['GET'])]
    public function getLocationsByTravel(Request $request, string $travel)
    {
        $user = $this->security->getUser();
        if (empty($user)) {
            return new JsonResponse(['error' => 'Operation not allowed']);
        }

        $query = new GetLocationsByTravelQuery($travel);
        $locations = $this->ask($query);

        $response = new JsonResponse([
            'type' => 'travel',
            'id' => $travel,
            'locations' => $locations,
        ]);

        $response->headers->set("Cache-Control", "no-cache, no-store, must-revalidate");

        return $response;
    }
}
