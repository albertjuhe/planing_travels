<?php

namespace App\UI\Controller\http;

use App\Application\Command\Travel\PublishTravelCommand;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Domain\User\Exceptions\UserDoesntExists;
use League\Tactician\CommandBus;

class PublishTravelController extends BaseController
{
    /**
     * ShowMyTravelsController constructor.
     *
     * @param $travelRepository
     * @param $commandBus
     */
    public function __construct(CommandBus $commandBus)
    {
        parent::__construct($commandBus);
    }

    /**
     * @Route("/travel/publish/{slug}",name="publishTravel")
     *
     * @param Request $request
     * @param $slug
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     *
     * @throws UserDoesntExists
     */
    public function publishTravel(Request $request, string $slug)
    {
        if (!$this->getUser()) {
            throw new UserDoesntExists();
        }
        $publishTravelCommand = new PublishTravelCommand($slug, $this->getUser());
        $this->commandBus->handle($publishTravelCommand);

        return $this->redirectToRoute('main_private');
    }
}
