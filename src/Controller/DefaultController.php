<?php
// src/Controller/DefaultController.php
namespace App\Controller;

use App\Application\UseCases\Travel\GetBestTravelsOrderedByService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use App\Infrastructure\TravelBundle\Repository\DoctrineTravelRepository;


class DefaultController extends Controller
{
    /** @var DoctrineTravelRepository  */
    private $travelRepository;

    /**
     * DefaultController constructor.
     * @param DoctrineTravelRepository $travelRepository
     */
    public function __construct(DoctrineTravelRepository $travelRepository)
    {
        $this->travelRepository = $travelRepository;
    }
    /**
     * Matches /
     * @Route("/{_locale}",defaults={"_locale"="en"},name="homepage")
     *
     * @return Response
     */
    public function index($_locale)
    {
        $getBestTravelsOrderedByService = new GetBestTravelsOrderedByService($this->travelRepository);
        $bestTravels = $getBestTravelsOrderedByService->execute();

        return $this->render('default/index.html.twig',array('travels'=>$bestTravels,'locale'=>$_locale));
    }
}