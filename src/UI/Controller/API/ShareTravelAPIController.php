<?php

namespace App\UI\Controller\API;

use App\Application\Command\Travel\ShareTravelCommand;
use App\Domain\Travel\Exceptions\InvalidTravelUser;
use App\Domain\User\Exceptions\UserDoesntExists;
use App\UI\Controller\http\CommandController;
use League\Tactician\CommandBus;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class ShareTravelAPIController extends CommandController
{
    /** @var Security */
    private $security;

    public function __construct(CommandBus $commandBus, Security $security)
    {
        parent::__construct($commandBus);
        $this->security = $security;
    }

    /**
     * @Route("/api/travel/{travelId}/share", name="shareTravelAPI", methods={"POST"})
     */
    public function share(Request $request, string $travelId): JsonResponse
    {
        $user = $this->security->getUser();
        if (empty($user)) {
            return new JsonResponse(['error' => 'Unauthorized'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $data = json_decode($request->getContent(), true);
        $username = $data['username'] ?? null;

        if (empty($username)) {
            return new JsonResponse(['error' => 'username is required'], JsonResponse::HTTP_BAD_REQUEST);
        }

        try {
            $command = new ShareTravelCommand($travelId, (int) $user->getId()->id(), $username);
            $this->commandBus->handle($command);
        } catch (InvalidTravelUser $e) {
            return new JsonResponse(['error' => $e->getMessage()], JsonResponse::HTTP_FORBIDDEN);
        } catch (UserDoesntExists $e) {
            return new JsonResponse(['error' => $e->getMessage()], JsonResponse::HTTP_NOT_FOUND);
        }

        return new JsonResponse(['shared' => true, 'username' => $username]);
    }
}
