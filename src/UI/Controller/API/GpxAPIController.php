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
            // High-contrast palette that stands out on most basemaps.
            $palette = ['#ff0033', '#ff6f00', '#ffd400', '#7c00ff', '#ff00aa', '#00d4ff', '#00ff66', '#ff3d00'];
            $color = $palette[array_rand($palette)];
        }

        $gpx = new Gpx();
        $gpx->setTitle($title);
        $gpx->setDescription((string) $request->request->get('description', ''));
        $gpx->setFilename($filename);
        $gpx->setColor($color);
        $gpx->setTravel($travel);
        if ($stats !== null && isset($stats['distanceMeters'])) {
            $gpx->setDistance((int) $stats['distanceMeters']);
        }

        $this->em->persist($gpx);
        $this->em->flush();

        return new JsonResponse([
            'success'  => true,
            'id'       => $gpx->getId(),
            'title'    => $gpx->getTitle(),
            'filename' => $gpx->getFilename(),
            'color'    => $gpx->getColor(),
            'distance' => $gpx->getDistance(),
            'simplification' => $stats,
        ], 201);
    }

    #[Route('/api/gpx/{gpxId}', name: 'updateGpxAPI', methods: ['PATCH'])]
    public function update(Request $request, int $gpxId): JsonResponse
    {
        $user = $this->security->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        $gpx = $this->em->getRepository(Gpx::class)->find($gpxId);
        if (!$gpx) {
            return new JsonResponse(['error' => 'GPX not found'], 404);
        }

        if (!$this->canEdit($gpx->getTravel(), $user)) {
            return new JsonResponse(['error' => 'Forbidden'], 403);
        }

        $data = json_decode($request->getContent(), true) ?: [];

        if (array_key_exists('color', $data)) {
            $color = trim((string) $data['color']);
            if ($color !== '' && !preg_match('/^#[0-9a-fA-F]{6}$/', $color)) {
                return new JsonResponse(['error' => 'Invalid color'], 400);
            }
            if ($color !== '') {
                $gpx->setColor($color);
            }
        }

        if (array_key_exists('title', $data)) {
            $title = trim((string) $data['title']);
            if ($title !== '') {
                $gpx->setTitle($title);
            }
        }

        if (array_key_exists('visitDate', $data)) {
            $visitDate = $data['visitDate'];
            if ($visitDate === null || $visitDate === '') {
                $gpx->setVisitDate(null);
            } else {
                $dt = \DateTime::createFromFormat('Y-m-d', (string) $visitDate);
                if (!$dt) {
                    return new JsonResponse(['error' => 'Invalid date (expected Y-m-d)'], 400);
                }
                $dt->setTime(0, 0, 0);
                $gpx->setVisitDate($dt);
            }
        }

        $gpx->setUpdatedAt(new \DateTime());
        $this->em->flush();

        return new JsonResponse([
            'success'   => true,
            'id'        => $gpx->getId(),
            'title'     => $gpx->getTitle(),
            'color'     => $gpx->getColor(),
            'visitDate' => $gpx->getVisitDateString(),
        ]);
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
