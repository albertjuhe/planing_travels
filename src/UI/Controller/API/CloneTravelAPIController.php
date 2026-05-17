<?php

namespace App\UI\Controller\API;

use App\Application\Command\Travel\CloneTravelCommand;
use App\UI\Controller\http\CommandController;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\SecurityBundle\Security;

class CloneTravelAPIController extends CommandController
{
    /** @var Security */
    private $security;

    public function __construct(MessageBusInterface $commandBus, Security $security)
    {
        parent::__construct($commandBus);
        $this->security = $security;
    }

    #[Route('/api/travel/{slug}/clone', name: 'cloneTravelAPI', methods: ['POST'])]
    public function clone(string $slug): JsonResponse
    {
        $user = $this->security->getUser();
        if (empty($user)) {
            return new JsonResponse(['error' => 'Unauthorized'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        try {
            $command = new CloneTravelCommand($slug, $user);
            $this->commandBus->dispatch($command);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }

        return new JsonResponse(['cloned' => true, 'originalSlug' => $slug]);
    }
}
