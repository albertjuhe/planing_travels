<?php

namespace App\UI\Controller\http;

use App\Application\Command\Travel\PublishTravelCommand;
use App\Application\Command\Travel\UnpublishTravelCommand;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Domain\User\Exceptions\UserDoesntExists;
use Symfony\Component\Messenger\MessageBusInterface;

class PublishTravelController extends CommandController
{
    /**
     * ShowMyTravelsController constructor.
     *
     * @param $travelRepository
     * @param $commandBus
     */
    public function __construct(MessageBusInterface $commandBus)
    {
        parent::__construct($commandBus);
    }

    #[Route('/travel/publish/{slug}', name: 'publishTravel')]
    public function publishTravel(Request $request, string $slug)
    {
        if (!$this->getUser()) {
            throw new UserDoesntExists();
        }
        $publishTravelCommand = new PublishTravelCommand($slug, $this->getUser());
        $this->commandBus->dispatch($publishTravelCommand);

        return $this->redirectToRoute('updateTravel', ['slug' => $slug]);
    }

    #[Route('/travel/unpublish/{slug}', name: 'unpublishTravel')]
    public function unpublishTravel(Request $request, string $slug)
    {
        if (!$this->getUser()) {
            throw new UserDoesntExists();
        }
        $command = new UnpublishTravelCommand($slug, $this->getUser());
        $this->commandBus->dispatch($command);

        return $this->redirectToRoute('updateTravel', ['slug' => $slug]);
    }
}

