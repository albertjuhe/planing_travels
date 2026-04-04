<?php

namespace App\UI\Controller\API;

use App\Domain\Travel\Exceptions\InvalidTravelUser;
use App\Infrastructure\TravelBundle\Repository\DoctrineTravelRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class GetSharedUsersAPIController extends AbstractController
{
    /** @var DoctrineTravelRepository */
    private $travelRepository;

    /** @var Security */
    private $security;

    public function __construct(DoctrineTravelRepository $travelRepository, Security $security)
    {
        $this->travelRepository = $travelRepository;
        $this->security = $security;
    }

    /**
     * @Route("/api/travel/{travelId}/shared-users", name="getSharedUsersAPI", methods={"GET"})
     */
    public function getSharedUsers(string $travelId): JsonResponse
    {
        $user = $this->security->getUser();
        if (empty($user)) {
            return new JsonResponse(['error' => 'Unauthorized'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $travel = $this->travelRepository->ofIdOrFail($travelId);

        if ((int) $travel->getUser()->getId()->id() !== (int) $user->getId()->id()) {
            return new JsonResponse(['error' => 'Forbidden'], JsonResponse::HTTP_FORBIDDEN);
        }

        $users = [];
        foreach ($travel->getSharedusers() as $sharedUser) {
            $users[] = [
                'username'  => $sharedUser->getUsername(),
                'firstName' => $sharedUser->getFirstName(),
                'lastName'  => $sharedUser->getLastName(),
            ];
        }

        return new JsonResponse(['users' => $users]);
    }
}
