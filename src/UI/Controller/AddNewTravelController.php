<?php
namespace App\UI\Controller;

use App\Application\Command\Travel\AddTravelCommand;
use App\Domain\Travel\Model\Travel;
use App\Infrastructure\TravelBundle\Form\TravelType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Infrastructure\TravelBundle\Repository\DoctrineTravelRepository;
use App\Domain\User\Exceptions\UserDoesntExists;
use App\Application\Command\CommandBus;



class AddNewTravelController extends BaseController
{
    /** @var DoctrineTravelRepository  */
    private $travelRepository;

    /**
     * ShowMyTravelsController constructor.
     * @param $travelRepository
     * @param $commandBus
     */
    public function __construct(DoctrineTravelRepository $travelRepository,
                                CommandBus $commandBus)
    {
        parent::__construct($commandBus);
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
            $addTravelCommand = new AddTravelCommand($travel);
            $this->commandBus->handle($addTravelCommand);

            return $this->redirectToRoute('main_private');
        }

        return $this->render('travel/new.html.twig',[
            'travelForm' => $form->createView()
        ]);
    }
}