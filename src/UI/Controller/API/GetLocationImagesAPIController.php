<?php

namespace App\UI\Controller\API;

use App\Infrastructure\LocationBundle\Repository\DoctrineLocationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class GetLocationImagesAPIController extends AbstractController
{
    private $locationRepository;
    private $security;

    public function __construct(
        DoctrineLocationRepository $locationRepository,
        Security $security
    ) {
        $this->locationRepository = $locationRepository;
        $this->security = $security;
    }

    /**
     * @Route("/api/location/{locationId}/images", name="getLocationImages", methods={"GET"})
     */
    public function getImages(string $locationId): JsonResponse
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

        $images = [];
        foreach ($location->getImages() as $image) {
            $images[] = [
                'id' => $image->getId(),
                'filename' => $image->getFilename(),
                'original' => $image->getOriginal(),
            ];
        }

        return new JsonResponse(['images' => $images]);
    }
}
