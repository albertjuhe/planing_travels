<?php

namespace App\UI\Controller;

use App\Application\Command\BestTravelsListCommand;
use App\Application\Command\CommandBus;
use App\Infrastructure\TravelBundle\Repository\DoctrineTravelRepository;

class BestTravelsListController extends BaseController
{
    /** @var DoctrineTravelRepository  */
    private $travelRepository;

    /**
     * BestTravelsListController constructor.
     * @param DoctrineTravelRepository $travelRepository
     */
    public function __construct(DoctrineTravelRepository $travelRepository, CommandBus $commandBus)
    {
        $this->travelRepository = $travelRepository;
        $this->commandBus = $commandBus;
    }

    /**
     * List travels
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listBestTravels($maxtravels) {
        $command = new BestTravelsListCommand($maxtravels,'stars');
        $travels = $this->commandBus->handle($command);

        return $this->render('default/bestTravels.html.twig',[
            'travels' => $travels
        ]);
    }

}