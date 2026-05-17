<?php

namespace App\UI\Controller\http;

use App\Application\Command\Travel\CloneTravelCommand;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Messenger\MessageBusInterface;

class CloneTravelController extends CommandController
{
    public function __construct(MessageBusInterface $commandBus)
    {
        parent::__construct($commandBus);
    }

    #[Route('/travel/clone/{slug}', name: 'cloneTravel')]
    public function cloneTravel(Request $request, string $slug)
    {
        if (!$this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $command = new CloneTravelCommand($slug, $this->getUser());
        $this->commandBus->dispatch($command);

        $this->addFlash('success', 'Travel cloned successfully! Check your travels.');

        return $this->redirectToRoute('main_private');
    }
}
