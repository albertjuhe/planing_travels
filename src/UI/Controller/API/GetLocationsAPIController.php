<?php

namespace App\UI\Controller\API;

use App\Application\Query\Location\GetLocationsByTravelQuery;
use App\Infrastructure\Application\QueryBus\QueryBus;
use App\UI\Controller\http\QueryController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class GetLocationsAPIController extends QueryController
{
    /**
     * @var Security
     */
    private $security;

    public function __construct(QueryBus $queryBus, Security $security)
    {
        parent::__construct($queryBus);
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

        $query = new GetLocationsByTravelQuery($travel);
        $locations = $this->ask($query);

        return new JsonResponse(
            $response['data'] = [
                'type' => 'travel',
                'id' => $travel,
                'locations' => $locations,
            ]
        );
    }
}
