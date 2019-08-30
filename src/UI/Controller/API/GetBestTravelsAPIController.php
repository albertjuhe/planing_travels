<?php

namespace App\UI\Controller\API;

use App\Application\Query\Travel\BestTravelsListQuery;
use App\Infrastructure\Application\QueryBus\QueryBus;
use App\UI\Controller\http\QueryController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class GetBestTravelsAPIController extends QueryController
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
     * @Route("/api/travels/best/{maxtravels}",name="getBestTravels")
     * @Method({"GET"})
     */
    public function listBestTravels($maxtravels)
    {
        $query = new BestTravelsListQuery($maxtravels, 'stars');
        $travels = $this->ask($query);

        $response = new JsonResponse(
            $response['data'] = $travels
        );

        return $response;
    }
}
