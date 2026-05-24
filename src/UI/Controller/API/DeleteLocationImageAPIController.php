<?php

namespace App\UI\Controller\API;

use App\Infrastructure\LocationBundle\Repository\DoctrineLocationRepository;
use App\Infrastructure\WebSocket\WebSocketNotifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\SecurityBundle\Security;

class DeleteLocationImageAPIController extends AbstractController
{
    private $locationRepository;
    private $security;
    private $em;
    private $webSocketNotifier;

    public function __construct(
        DoctrineLocationRepository $locationRepository,
        Security $security,
        EntityManagerInterface $em,
        WebSocketNotifier $webSocketNotifier
    ) {
        $this->locationRepository = $locationRepository;
        $this->security = $security;
        $this->em = $em;
        $this->webSocketNotifier = $webSocketNotifier;
    }

    #[Route('/api/location/{locationId}/image/{imageId}', name: 'deleteLocationImage', methods: ['DELETE'])]
    public function delete(string $locationId, int $imageId): JsonResponse
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

        $image = null;
        foreach ($location->getImages() as $img) {
            if ($img->getId() === $imageId) {
                $image = $img;
                break;
            }
        }

        if (!$image) {
            return new JsonResponse(['error' => 'Image not found'], 404);
        }

        $filename = $image->getFilename();
        $uploadDir = $this->getParameter('kernel.project_dir') . '/public/uploads/gallery/';
        $filePath = $uploadDir . $filename;

        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $location->removeImages($image);
        $this->em->remove($image);
        $this->em->flush();

        $this->webSocketNotifier->notifyImageDeleted(
            $travel->getId()->id(),
            $locationId,
            $imageId,
            (string) $user->getId()->id(),
            $user->getUsername()
        );

        return new JsonResponse(['success' => true]);
    }
}
