<?php

namespace App\UI\Controller;

use App\Application\Command\Travel\BestTravelsListCommand;
use League\Tactician\CommandBus;

class BestTravelsListController extends BaseController
{

    /**
     * BestTravelsListController constructor.
     * @param CommandBus $commandBus
     */
    public function __construct(CommandBus $commandBus)
    {
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