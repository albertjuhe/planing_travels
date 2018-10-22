<?php
namespace App\UI\Controller;

use App\Application\Command\Travel\AddTravelCommand;
use App\Domain\Travel\Model\Travel;
use App\Infrastructure\TravelBundle\Form\TravelType;
use App\Infrastructure\UserBundle\Repository\DoctrineUserRepository;
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
    /** @var DoctrineUserRepository */
    private $userRepository;

    /**
     * ShowMyTravelsController constructor.
     * @param $travelRepository
     * @param $commandBus
     */
    public function __construct(CommandBus $commandBus)
    {
        parent::__construct($commandBus);
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

        $travel = new Travel();

        $form = $this->createForm(TravelType::class,$travel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $addTravelCommand = new AddTravelCommand($travel,$this->getUser());
            $this->commandBus->handle($addTravelCommand);

            return $this->redirectToRoute('main_private');
        }

        return $this->render('travel/new.html.twig',[
            'travelForm' => $form->createView()
        ]);
    }
}