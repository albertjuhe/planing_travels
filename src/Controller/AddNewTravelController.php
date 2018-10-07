<?php
namespace App\Controller;

use App\Domain\Travel\Model\Travel;
use App\Infrastructure\TravelBundle\Form\TravelType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Infrastructure\TravelBundle\Repository\DoctrineTravelRepository;
use App\Application\UseCases\Travel\AddTravelService;
use App\Domain\User\Exceptions\UserDoesntExists;

class AddNewTravelController extends Controller
{
    /** @var DoctrineTravelRepository  */
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
     * @Route("/{_locale}/private/new",name="newTravel")
     * @param Request $request
     * @param $_locale
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws UserDoesntExists
     */
    public function newTravel(Request $request,$_locale) {
        if(!$this->getUser())
            throw new UserDoesntExists();

        $travel = Travel::fromUser($this->getUser());

        $form = $this->createForm(TravelType::class,$travel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $addTravelService = new AddTravelService($this->travelRepository);
            $addTravelService->add($travel);

            return $this->redirectToRoute('main_private');
        }

        return $this->render('travel/new.html.twig',[
            'travelForm' => $form->createView()
        ]);
    }
}