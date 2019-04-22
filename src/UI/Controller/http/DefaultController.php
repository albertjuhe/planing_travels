<?php

namespace App\UI\Controller\http;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Infrastructure\TravelBundle\Repository\DoctrineTravelRepository;

class DefaultController extends AbstractController
{
    /** @var DoctrineTravelRepository */
    private $travelRepository;

    /**
     * DefaultController constructor.
     *
     * @param DoctrineTravelRepository $travelRepository
     */
    public function __construct(DoctrineTravelRepository $travelRepository)
    {
        $this->travelRepository = $travelRepository;
    }

    /**
     * Matches /.
     *
     * @Route("/{_locale}",defaults={"_locale"="en"},name="homepage")
     *
     * @return Response
     */
    public function index($_locale)
    {
        return $this->render('default/index.html.twig', ['locale' => $_locale]);
    }
}
