<?php

namespace App\UI\Controller\http;

use App\Application\Command\Travel\AddTravelCommand;
use App\Domain\Travel\Model\Travel;
use App\Infrastructure\TravelBundle\Form\TravelType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Domain\User\Exceptions\UserDoesntExists;
use League\Tactician\CommandBus;


class AddNewTravelController extends CommandController
{
    public function __construct(CommandBus $commandBus)
    {
        parent::__construct($commandBus);
    }

    /**
     * @Route("/{_locale}/private/new",name="newTravel")
     *
     * @param Request $request
     * @param $_locale
     *
     * @return RedirectResponse|Response
     *
     * @throws UserDoesntExists
     */
    public function newTravel(Request $request, $_locale)
    {
        if (!$this->getUser()) {
            throw new UserDoesntExists();
        }
        $travel = new Travel();

        $form = $this->createForm(TravelType::class, $travel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photoFile = $form->get('photo')->getData();
            if ($photoFile) {
                $originalFilename = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = preg_replace('/[^a-zA-Z0-9_-]/', '-', $originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$photoFile->guessExtension();
                try {
                    $photoFile->move(
                        $this->getParameter('travel_photos_directory'),
                        $newFilename
                    );
                    $travel->setPhoto($newFilename);
                } catch (FileException $e) {
                }
            }

            $addTravelCommand = new AddTravelCommand($travel, $this->getUser());
            $this->commandBus->handle($addTravelCommand);

            return $this->redirectToRoute('main_private');
        }

        return $this->render('travel/new.html.twig', [
            'travelForm' => $form->createView(),
        ]);
    }
}
