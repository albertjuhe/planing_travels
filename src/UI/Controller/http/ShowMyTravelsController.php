<?php

namespace App\UI\Controller\http;

use App\Application\Query\Travel\GetMyTravelsQuery;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShowMyTravelsController extends QueryController
{
    /**
     * @Route("/{_locale}/private",name="main_private")
     *
     * @return Response
     */
    public function showMyTravels()
    {
        $user = $this->guard();
        $getMyTravelQuery = new GetMyTravelsQuery($user);
        $travels = $this->ask($getMyTravelQuery);

        return $this->render('private/index.html.twig', ['travels' => $travels]);
    }
}
