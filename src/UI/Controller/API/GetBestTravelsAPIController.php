<?php

namespace App\UI\Controller\API;

use App\Application\Query\Travel\BestTravelsListQuery;
use App\Infrastructure\Application\QueryBus\QueryBus;
use App\UI\Controller\http\QueryController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class GetBestTravelsAPIController extends QueryController
{
    public function __construct(QueryBus $queryBus, Security $security)
    {
        parent::__construct($queryBus, $security);
    }

    /**
     * @Route("/api/travels/best/{maxtravels}",name="getBestTravels")
     * @Method({"GET"})
     */
    public function listBestTravels(Request $request, int $maxtravels)
    {
        $query = new BestTravelsListQuery($maxtravels, 'stars');
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
