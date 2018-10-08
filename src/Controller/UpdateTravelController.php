<?php
/**
 * Created by PhpStorm.
 * User: albert.juhe
 * Date: 03/10/2018
 * Time: 07:10
 */

namespace App\Controller;

use App\Domain\Travel\Model\Travel;
use App\Domain\User\Model\User;
use App\Infrastructure\UserBundle\Repository\DoctrineUserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Infrastructure\TravelBundle\Repository\DoctrineTravelRepository;
use App\Application\UseCases\Travel\UpdateTravelService;
use App\Domain\User\Exceptions\UserDoesntExists;
use App\Infrastructure\TravelBundle\Form\UpdateTravelType;
use App\Application\Command\UpdateTravelCommand;
use App\Application\Command\CommandBus;


class UpdateTravelController extends Controller
{
    /** @var DoctrineTravelRepository */
    private $travelRepository;
    /** @var DoctrineUserRepository */
    private $userRepository;

    /**
     * UpdateTravelController constructor.
     * @param DoctrineTravelRepository $travelRepository
     * @param DoctrineUserRepository $userRepository
     */
    public function __construct(DoctrineTravelRepository $travelRepository, DoctrineUserRepository $userRepository)
    {
        $this->travelRepository = $travelRepository;
        $this->userRepository = $userRepository;

    }


    /**
     * @Route("/{_locale}/private/travel/{slug}/update",name="updateTravel")
     * @param Request $request
     * @param String $slug
     * @param $_locale
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws UserDoesntExists
     * @throws \App\Domain\Travel\Exceptions\InvalidTravelUser
     * @throws \App\Domain\Travel\Exceptions\TravelDoesntExists
     */
    public function updateTravel(Request $request, String $slug, $_locale)
    {

        if (!$this->getUser()) throw new UserDoesntExists();
        /** @var Travel $travel */
        $travel = $this->travelRepository->ofSlugOrFail($slug);

        $form = $this->createForm(UpdateTravelType::class, $travel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $updateTravelService = new UpdateTravelService($this->travelRepository);
            $commandUpdate = new UpdateTravelCommand($travel, $this->getUser());
            $updateTravelService->execute($commandUpdate);

            return $this->redirectToRoute('main_private');
        }

        return $this->render('travel/updateTravel.html.twig', [
            'travelForm' => $form->createView()
        ]);

    }

}
