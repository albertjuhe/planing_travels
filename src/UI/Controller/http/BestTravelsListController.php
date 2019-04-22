<?php

namespace App\UI\Controller\http;

use App\Application\Query\Travel\BestTravelsListQuery;

class BestTravelsListController extends QueryController
{
    public function listBestTravels($maxtravels)
    {
        $query = new BestTravelsListQuery($maxtravels, 'stars');
        $travels = $this->ask($query);

        return $this->render('default/bestTravels.html.twig', [
            'travels' => $travels,
        ]);
    }
}
