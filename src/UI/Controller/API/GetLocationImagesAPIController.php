<?php

namespace App\UI\Controller\API;

use App\Infrastructure\LocationBundle\Repository\DoctrineLocationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class GetLocationImagesAPIController extends AbstractController
{
    private $locationRepository;

    public function __construct(
        DoctrineLocationRepository $locationRepository
    ) {
        $this->locationRepository = $locationRepository;
    }

    #[Route('/api/location/{locationId}/images', name: 'getLocationImages', methods: ['GET'])]
    public function getImages(string $locationId): JsonResponse
    {
        try {
            $location = $this->locationRepository->findById($locationId);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Location not found'], 404);
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
