<?php
/**
 * Created by PhpStorm.
 * User: Albert
 * Date: 20/01/2018
 * Time: 19:59
 */

namespace App\Controller;

use App\Domain\User\Exceptions\UserDoesntExists;
use App\Domain\User\Model\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use App\Application\UseCases\Travel\GetAllMyTravelsService;
use App\Infrastructure\TravelBundle\Repository\DoctrineTravelRepository;

class ShowMyTravelsController extends Controller
{
    private $travelRepository;

    /**
     * ShowMyTravelsController constructor.
     * @param $travelRepository
     */
    public function __construct(DoctrineTravelRepository $travelRepository)
    {
        $this->travelRepository = $travelRepository;
    }

    /**
     * @Route("/{_locale}/private",name="main_private")
     *
     * @return Response
     */
    public function showMyTravels() {
        $user = $this->getUser();
        if(!$user) new UserDoesntExists();

        $getAllMyTravelsService = new GetAllMyTravelsService($this->travelRepository);
        $travels = $getAllMyTravelsService->execute($user);
        return $this->render('private/index.html.twig', array('travels' =>$travels));
    }
}