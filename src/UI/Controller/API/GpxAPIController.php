<?php

namespace App\UI\Controller\API;

use App\Domain\Gpx\Model\Gpx;
use App\Infrastructure\GpxBundle\Service\GpxSimplifier;
use App\Infrastructure\TravelBundle\Repository\DoctrineTravelRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class GpxAPIController extends AbstractController
{
    public function __construct(
        private DoctrineTravelRepository $travelRepository,
        private EntityManagerInterface $em,
        private Security $security,
        private GpxSimplifier $gpxSimplifier,
        private LoggerInterface $logger
    ) {
    }

    #[Route('/api/travel/{travelId}/gpx', name: 'uploadGpxAPI', methods: ['POST'])]
    public function upload(Request $request, string $travelId): JsonResponse
    {
        $user = $this->security->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        try {
            $travel = $this->travelRepository->ofIdOrFail($travelId);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Travel not found'], 404);
        }

        if (!$this->canEdit($travel, $user)) {
            return new JsonResponse(['error' => 'Forbidden'], 403);
        }

        $file = $request->files->get('file');
        if (!$file) {
            return new JsonResponse(['error' => 'No file provided'], 400);
        }

        $maxSize = 3 * 1024 * 1024; // 3 MB
        if ($file->getSize() > $maxSize) {
            return new JsonResponse(['error' => 'File too large (max 3 MB)'], 413);
        }

        $originalName = $file->getClientOriginalName();
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        if ($extension !== 'gpx') {
            return new JsonResponse(['error' => 'Only .gpx files are allowed'], 400);
        }

        $uploadDir = $this->getParameter('kernel.project_dir').'/public/uploads/gpx/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $baseName = preg_replace('/[^a-zA-Z0-9._-]/', '_', pathinfo($originalName, PATHINFO_FILENAME));
        $filename = uniqid().'_'.$baseName.'.gpx';

        try {
            $file->move($uploadDir, $filename);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Could not save file: '.$e->getMessage()], 500);
        }

        // Simplify the GPX in place to reduce file size (Douglas-Peucker).
        $stats = null;
        try {
            $stats = $this->gpxSimplifier->simplifyFile($uploadDir.$filename, 0.0001);
        } catch (\Throwable $e) {
            $this->logger->warning('GPX simplification failed: '.$e->getMessage(), ['file' => $filename]);
        }

        $title = trim((string) $request->request->get('title', ''));
        if ($title === '') {
            $title = pathinfo($originalName, PATHINFO_FILENAME);
        }

        $color = trim((string) $request->request->get('color', ''));
        if ($color === '' || !preg_match('/^#[0-9a-fA-F]{6}$/', $color)) {
            $palette = ['#e74c3c', '#2980b9', '#27ae60', '#8e44ad', '#f39c12', '#1abc9c', '#d35400', '#16a085'];
            $color = $palette[array_rand($palette)];
        }

        $gpx = new Gpx();
        $gpx->setTitle($title);
        $gpx->setDescription((string) $request->request->get('description', ''));
        $gpx->setFilename($filename);
        $gpx->setColor($color);
        $gpx->setTravel($travel);

        $this->em->persist($gpx);
        $this->em->flush();

        return new JsonResponse([
            'success'  => true,
            'id'       => $gpx->getId(),
            'title'    => $gpx->getTitle(),
            'filename' => $gpx->getFilename(),
            'color'    => $gpx->getColor(),
            'simplification' => $stats,
        ], 201);
    }

    #[Route('/api/gpx/{gpxId}', name: 'deleteGpxAPI', methods: ['DELETE'])]
    public function delete(int $gpxId): JsonResponse
    {
        $user = $this->security->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        $gpx = $this->em->getRepository(Gpx::class)->find($gpxId);
        if (!$gpx) {
            return new JsonResponse(['error' => 'GPX not found'], 404);
        }

        $travel = $gpx->getTravel();
        if (!$this->canEdit($travel, $user)) {
            return new JsonResponse(['error' => 'Forbidden'], 403);
        }

        $uploadDir = $this->getParameter('kernel.project_dir').'/public/uploads/gpx/';
        $filePath = $uploadDir.$gpx->getFilename();
        if (is_file($filePath)) {
            @unlink($filePath);
        }

        $this->em->remove($gpx);
        $this->em->flush();

        return new JsonResponse(['success' => true]);
    }

    private function canEdit($travel, $user): bool
    {
        $userId = (int) $user->getId()->id();
        if ((int) $travel->getUser()->getId()->id() === $userId) {
            return true;
        }
        foreach ($travel->getSharedusers() as $shared) {
            if ((int) $shared->getId()->id() === $userId) {
                return true;
            }
        }
        return false;
    }
}
