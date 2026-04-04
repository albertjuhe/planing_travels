<?php

namespace App\UI\Controller\http;

use App\Application\Query\Travel\GetMyTravelsQuery;
use App\Domain\User\Exceptions\UserDoesntExists;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShowMyTravelsController extends QueryController
{
    /**
     * @Route("/{_locale}/private",name="main_private")
     *
     * @return Response
     * @throws UserDoesntExists
     */
    public function showMyTravels()
    {
        $user = $this->guard();
        $getMyTravelQuery = new GetMyTravelsQuery($user);
        $result = $this->ask($getMyTravelQuery);

        return $this->render('private/index.html.twig', [
            'travels'        => $result['owned'],
            'sharedTravels'  => $result['shared'],
        ]);
    }
}
