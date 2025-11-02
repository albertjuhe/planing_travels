<?php

namespace App\UI\Controller\http;

use App\Domain\Travel\Exceptions\TravelDoesntExists;
use App\Domain\Travel\Model\Travel;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Infrastructure\TravelBundle\Repository\DoctrineTravelRepository;
use App\Domain\User\Exceptions\UserDoesntExists;
use App\Infrastructure\TravelBundle\Form\UpdateTravelType;
use App\Application\Command\Travel\UpdateTravelCommand;
use League\Tactician\CommandBus;

class UpdateTravelController extends CommandController
{
    /** @var DoctrineTravelRepository */
    private $travelRepository;

    public function __construct(
        DoctrineTravelRepository $travelRepository,
        CommandBus $commandBus
    ) {
        parent::__construct($commandBus);
        $this->travelRepository = $travelRepository;
    }

    /**
     * @Route("/{_locale}/private/travel/{slug}/update",name="updateTravel")
     *
     * @param Request $request
     * @param string  $slug
     * @param $_locale
     *
     * @return RedirectResponse|Response
     *
     * @throws UserDoesntExists
     * @throws TravelDoesntExists
     */
    public function updateTravel(Request $request, string $slug, $_locale)
    {
        if (!$this->getUser()) {
            throw new UserDoesntExists();
        }
        /** @var Travel $travel */
        $travel = $this->travelRepository->ofSlugOrFail($slug);

        $form = $this->createForm(UpdateTravelType::class, $travel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commandUpdate = new UpdateTravelCommand($travel, $this->getUser());
            $this->commandBus->handle($commandUpdate);

            return $this->redirectToRoute('main_private');
        }

        return $this->render('travel/updateTravel.html.twig', [
            'travelForm' => $form->createView(),
            'latitude' => $travel->getLatitude(),
            'longitude' => $travel->getLongitude(),
        ]);
    }
}
