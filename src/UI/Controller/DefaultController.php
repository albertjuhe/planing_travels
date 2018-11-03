<?php
// src/Controller/DefaultController.php
namespace App\UI\Controller;

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
        return $this->render('default/index.html.twig',array('locale'=>$_locale));
    }
}