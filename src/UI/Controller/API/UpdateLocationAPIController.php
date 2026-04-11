<?php

namespace App\UI\Controller\API;

use App\Infrastructure\LocationBundle\Repository\DoctrineLocationRepository;
use App\Infrastructure\TypeLocationBundle\Repository\DoctrineTypeLocation;
use App\Infrastructure\WebSocket\WebSocketNotifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class UpdateLocationAPIController extends AbstractController
{
    private $locationRepository;
    private $typeLocationRepository;
    private $security;
    private $em;
    private $webSocketNotifier;

    public function __construct(
        DoctrineLocationRepository $locationRepository,
        DoctrineTypeLocation $typeLocationRepository,
        Security $security,
        EntityManagerInterface $em,
        WebSocketNotifier $webSocketNotifier
    ) {
        $this->locationRepository = $locationRepository;
        $this->typeLocationRepository = $typeLocationRepository;
        $this->security = $security;
        $this->em = $em;
        $this->webSocketNotifier = $webSocketNotifier;
    }

    /**
     * @Route("/api/location/{locationId}", name="updateLocation", methods={"PATCH"})
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

        if (isset($data['title']) && $data['title'] !== '') {
            $location->setTitle($data['title']);
        }

        if (array_key_exists('url', $data)) {
            $location->setUrl($data['url']);
        }

        if (array_key_exists('description', $data)) {
            $location->setDescription($data['description']);
        }

        if (isset($data['typeLocationId'])) {
            try {
                $typeLocation = $this->typeLocationRepository->idOrFail($data['typeLocationId']);
                $location->setTypeLocation($typeLocation);
            } catch (\Exception $e) {
                return new JsonResponse(['error' => 'Invalid location type'], 400);
            }
        }

        $this->em->flush();

        $this->webSocketNotifier->notifyLocationUpdated(
            $travel->getId()->id(),
            [
                'id'          => $location->getId()->id(),
                'title'       => $location->getTitle(),
                'url'         => $location->getUrl(),
                'description' => $location->getDescription(),
                'typeLocationId' => $location->getTypeLocation() ? $location->getTypeLocation()->getId() : null,
                'typeIcon'    => $location->getTypeLocation() ? $location->getTypeLocation()->getIcon() : '',
                'updatedByUserId' => (string) $user->getId()->id(),
            ],
            (string) $user->getId()->id()
        );

        return new JsonResponse(['success' => true]);
    }
}
