<?php


namespace App\UI\Controller;

use App\Application\Command\Travel\ShowTravelBySlugCommand;
use Symfony\Component\Routing\Annotation\Route;
use League\Tactician\CommandBus;

class ShowTravelController extends BaseController
{
    /**
     * ShowTravelController constructor.
     * @param DoctrineTravelRepository $travelRepository
     */
    public function __construct(CommandBus $commandBus)
    {
        parent::__construct($commandBus);
    }

    /**
     * @param string $slug
     * @Route("/{_locale}/travel/{slug}",name="show_travel")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showTravel(string $slug)
    {
        $commandShow = new ShowTravelBySlugCommand($slug);
        $travel = $this->commandBus->handle($commandShow);

        return $this->render('travel/showTravel.html.twig',
            array('travel' => $travel ));
    }
}