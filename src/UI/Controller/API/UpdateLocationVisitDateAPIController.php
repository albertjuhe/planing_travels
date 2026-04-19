<?php

namespace App\UI\Controller\API;

use App\Infrastructure\LocationBundle\Repository\DoctrineLocationRepository;
use App\Infrastructure\WebSocket\WebSocketNotifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class UpdateLocationVisitDateAPIController extends AbstractController
{
    private $locationRepository;
    private $security;
    private $em;
    private $webSocketNotifier;

    public function __construct(DoctrineLocationRepository $locationRepository, Security $security, EntityManagerInterface $em, WebSocketNotifier $webSocketNotifier)
    {
        $this->locationRepository = $locationRepository;
        $this->security = $security;
        $this->em = $em;
        $this->webSocketNotifier = $webSocketNotifier;
    }

    /**
     * @Route("/api/location/{locationId}/visit-date", name="updateLocationVisitDate", methods={"PATCH"})
     */
    public function update(Request $request, string $locationId): JsonResponse
    {
        $user = $this->security->getUser();
        if (empty($user)) {
            return new JsonResponse(['error' => 'Operation not allowed'], 403);
        }

        try {
            $location = $this->locationRepository->findById($locationId);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Location not found'], 404);
        }

        $travel = $location->getTravel();
        $travelOwner = $travel->getUser();
        $isOwner = $travelOwner->getId()->id() === $user->getId()->id();
        $isShared = false;
        foreach ($travel->getSharedusers() as $sharedUser) {
            if ($sharedUser->getId()->id() === $user->getId()->id()) {
                $isShared = true;
                break;
            }
        }

        if (!$isOwner && !$isShared) {
            return new JsonResponse(['error' => 'Operation not allowed'], 403);
        }

        $data = json_decode($request->getContent(), true);
        $visitAt = isset($data['visitAt']) ? $data['visitAt'] : false;

        if ($visitAt === null || $visitAt === '') {
            $location->setVisitAt(null);
        } elseif ($visitAt !== false) {
            try {
                $date = new \DateTime($visitAt);
                $location->setVisitAt($date);
            } catch (\Exception $e) {
                return new JsonResponse(['error' => 'Invalid date format'], 400);
            }
        }

        $this->em->flush();

        $this->webSocketNotifier->notifyVisitDateChanged(
            $travel->getId()->id(),
            $locationId,
            $location->getVisitAt() ? $location->getVisitAt()->format('Y-m-d') : null,
            (string) $user->getId()->id(),
            $user->getUsername()
        );

        return new JsonResponse(['success' => true]);
    }
}
