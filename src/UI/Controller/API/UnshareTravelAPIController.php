<?php

namespace App\UI\Controller\API;

use App\Application\Command\Travel\UnshareTravelCommand;
use App\Domain\Travel\Exceptions\InvalidTravelUser;
use App\Domain\User\Exceptions\UserDoesntExists;
use App\UI\Controller\http\CommandController;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\SecurityBundle\Security;

class UnshareTravelAPIController extends CommandController
{
    /** @var Security */
    private $security;

    public function __construct(MessageBusInterface $commandBus, Security $security)
    {
        parent::__construct($commandBus);
        $this->security = $security;
    }

    #[Route('/api/travel/{travelId}/share/{username}', name: 'unshareTravelAPI', methods: ['DELETE'])]
    public function unshare(string $travelId, string $username): JsonResponse
    {
        $user = $this->security->getUser();
        if (empty($user)) {
            return new JsonResponse(['error' => 'Unauthorized'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        try {
            $command = new UnshareTravelCommand($travelId, (int) $user->getId()->id(), $username);
            $this->commandBus->dispatch($command);
        } catch (InvalidTravelUser $e) {
            return new JsonResponse(['error' => $e->getMessage()], JsonResponse::HTTP_FORBIDDEN);
        } catch (UserDoesntExists $e) {
            return new JsonResponse(['error' => $e->getMessage()], JsonResponse::HTTP_NOT_FOUND);
        }

        return new JsonResponse(['unshared' => true, 'username' => $username]);
    }
}
