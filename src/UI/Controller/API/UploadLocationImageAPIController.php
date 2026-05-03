<?php

namespace App\UI\Controller\API;

use App\Domain\Images\Model\Images;
use App\Infrastructure\LocationBundle\Repository\DoctrineLocationRepository;
use App\Infrastructure\WebSocket\WebSocketNotifier;
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

        // Limit file size to 5MB
        if ($file->getSize() > 5 * 1024 * 1024) {
            return new JsonResponse(['error' => 'File too large (max 5MB)'], 400);
        }

        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file->getMimeType(), $allowedMimeTypes)) {
            return new JsonResponse(['error' => 'Invalid file type'], 400);
        }

        // Load image and optimize
        $originalName = $file->getClientOriginalName();
        $uploadDir = $this->getParameter('kernel.project_dir') . '/public/uploads/gallery/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Generate unique filename with WebP extension
        $baseFilename = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', pathinfo($originalName, PATHINFO_FILENAME));
        $webpFilename = $baseFilename . '.webp';

        // Create image resource from uploaded file
        $sourcePath = $file->getPathname();
        $mime = $file->getMimeType();
        $image = null;

        switch ($mime) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($sourcePath);
                break;
            case 'image/png':
                $image = imagecreatefrompng($sourcePath);
                break;
            case 'image/gif':
                $image = imagecreatefromgif($sourcePath);
                break;
            case 'image/webp':
                $image = imagecreatefromwebp($sourcePath);
                break;
        }

        if ($image) {
            // Resize if too large (max 1920px width)
            $maxWidth = 1920;
            $width = imagesx($image);
            $height = imagesy($image);

            if ($width > $maxWidth) {
                $newWidth = $maxWidth;
                $newHeight = intval($height * ($maxWidth / $width));
                $resized = imagecreatetruecolor($newWidth, $newHeight);
                imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                imagedestroy($image);
                $image = $resized;
            }

            // Save as WebP with 85% quality
            imagewebp($image, $uploadDir . $webpFilename, 85);
            imagedestroy($image);
            $filename = $webpFilename;
        } else {
            // Fallback: just move the file with sanitized name
            $filename = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $originalName);
            $file->move($uploadDir, $filename);
        }

        $image = new Images();
        $image->setFilename($filename);
        $image->setOriginal($originalName);
        $image->setLocation($location);
        $location->addImages($image);

        $this->em->persist($image);
        $this->em->flush();

        $this->webSocketNotifier->notifyImageUploaded(
            $travel->getId()->id(),
            $locationId,
            $filename,
            (string) $user->getId()->id(),
            $user->getUsername()
        );

        return new JsonResponse([
            'success' => true,
            'filename' => $filename,
            'id' => $image->getId(),
        ], 201);
    }
}
