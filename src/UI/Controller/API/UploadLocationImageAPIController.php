<?php

namespace App\UI\Controller\API;

use App\Domain\Images\Model\Images;
use App\Infrastructure\LocationBundle\Repository\DoctrineLocationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class UploadLocationImageAPIController extends AbstractController
{
    private $locationRepository;
    private $security;
    private $em;

    public function __construct(
        DoctrineLocationRepository $locationRepository,
        Security $security,
        EntityManagerInterface $em
    ) {
        $this->locationRepository = $locationRepository;
        $this->security = $security;
        $this->em = $em;
    }

    /**
     * @Route("/api/location/{locationId}/image", name="uploadLocationImage", methods={"POST"})
     */
    public function upload(Request $request, string $locationId): JsonResponse
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

        $file = $request->files->get('file');
        if (!$file) {
            return new JsonResponse(['error' => 'No file provided'], 400);
        }

        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file->getMimeType(), $allowedMimeTypes)) {
            return new JsonResponse(['error' => 'Invalid file type'], 400);
        }

        $uploadDir = $this->getParameter('kernel.project_dir') . '/public/uploads/gallery/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $originalName = $file->getClientOriginalName();
        $filename = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $originalName);
        $file->move($uploadDir, $filename);

        $image = new Images();
        $image->setFilename($filename);
        $image->setOriginal($originalName);
        $image->setLocation($location);
        $location->addImages($image);

        $this->em->persist($image);
        $this->em->flush();

        return new JsonResponse([
            'success' => true,
            'filename' => $filename,
            'id' => $image->getId(),
        ], 201);
    }
}
