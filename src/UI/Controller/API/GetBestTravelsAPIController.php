<?php

namespace App\UI\Controller\API;

use App\Application\Query\Travel\BestTravelsListQuery;
use App\Infrastructure\Application\QueryBus\QueryBus;
use App\UI\Controller\http\QueryController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Routing\Attribute\Route;

class GetBestTravelsAPIController extends QueryController
{
    public function __construct(QueryBus $queryBus, Security $security)
    {
        parent::__construct($queryBus, $security);
    }

    #[Route('/api/travels/best/{maxtravels}', name: 'getBestTravels', methods: ['GET'])]
    public function listBestTravels(Request $request, int $maxtravels)
    {
        $query = new BestTravelsListQuery($maxtravels, 'createdAt');
        $travels = $this->ask($query);
        $url = $this->generateUrl('getBestTravels', ['maxtravels' => $maxtravels]);
        $data = [
            'links' => [
              'self' => $url,
            ],
            'data' => $travels,
        ];

        return new JsonResponse($data);
    }
}
