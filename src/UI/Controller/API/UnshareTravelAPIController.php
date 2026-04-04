<?php

namespace App\UI\Controller\API;

use App\Application\Command\Travel\UnshareTravelCommand;
use App\Domain\Travel\Exceptions\InvalidTravelUser;
use App\Domain\User\Exceptions\UserDoesntExists;
use App\UI\Controller\http\CommandController;
use League\Tactician\CommandBus;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class UnshareTravelAPIController extends CommandController
{
    /** @var Security */
    private $security;

    public function __construct(CommandBus $commandBus, Security $security)
    {
        parent::__construct($commandBus);
        $this->security = $security;
    }

    /**
     * @Route("/api/travel/{travelId}/share/{username}", name="unshareTravelAPI", methods={"DELETE"})
     */
    public function unshare(string $travelId, string $username): JsonResponse
    {
        $user = $this->security->getUser();
        if (empty($user)) {
            return new JsonResponse(['error' => 'Unauthorized'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        try {
            $command = new UnshareTravelCommand($travelId, (int) $user->getId()->id(), $username);
            $this->commandBus->handle($command);
        } catch (InvalidTravelUser $e) {
            return new JsonResponse(['error' => $e->getMessage()], JsonResponse::HTTP_FORBIDDEN);
        } catch (UserDoesntExists $e) {
            return new JsonResponse(['error' => $e->getMessage()], JsonResponse::HTTP_NOT_FOUND);
        }

        return new JsonResponse(['unshared' => true, 'username' => $username]);
    }
}
