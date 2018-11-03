<?php


namespace App\UI\Controller;

use App\Application\Command\Travel\ShowTravelBySlugCommand;
use App\Application\UseCases\Travel\ShowTravelService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use App\Infrastructure\TravelBundle\Repository\DoctrineTravelRepository;
use App\Application\Command\CommandBus;

class ShowTravelController extends BaseController
{
    /** @var DoctrineTravelRepository */
    private $travelRepository;

    /**
     * ShowTravelController constructor.
     * @param DoctrineTravelRepository $travelRepository
     */
    public function __construct(DoctrineTravelRepository $travelRepository, CommandBus $commandBus)
    {
        parent::__construct($commandBus);
        $this->travelRepository = $travelRepository;
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