<?php

namespace App\UI\Controller\API;

use App\Infrastructure\LocationBundle\Repository\DoctrineLocationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class UpdateLocationPositionsAPIController extends AbstractController
{
    private $locationRepository;
    private $security;
    private $em;

    public function __construct(DoctrineLocationRepository $locationRepository, Security $security, EntityManagerInterface $em)
    {
        $this->locationRepository = $locationRepository;
        $this->security = $security;
        $this->em = $em;
    }

    /**
     * @Route("/api/locations/positions", name="updateLocationPositions", methods={"POST"})
     */
    public function update(Request $request): JsonResponse
    {
        $user = $this->security->getUser();
        if (empty($user)) {
            return new JsonResponse(['error' => 'Operation not allowed'], 403);
        }

        $data = json_decode($request->getContent(), true);
        $updates = $data['updates'] ?? [];

        if (empty($updates)) {
            return new JsonResponse(['success' => true]);
        }

        foreach ($updates as $update) {
            $locationId = $update['locationId'] ?? null;
            $date = $update['date'] ?? null;
            $position = $update['position'] ?? null;

            if (!$locationId || !$date || $position === null) {
                continue;
            }

            try {
                $location = $this->locationRepository->findById($locationId);

                // Check permissions
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
                    continue;
                }

                // Update position for this date
                foreach ($location->getVisitDates() as $vd) {
                    if ($vd->getVisitDate()->format('Y-m-d') === $date) {
                        $vd->setPosition((int) $position);
                        break;
                    }
                }

                // Reindex positions for this date to ensure they are sequential
                $this->reindexPositionsForDate($travel, $date);

            } catch (\Exception $e) {
                // Skip invalid location IDs
                continue;
            }
        }

        $this->em->flush();

        return new JsonResponse(['success' => true]);
    }

    /**
     * Reindex positions for all locations on a given date within a travel
     */
    private function reindexPositionsForDate($travel, string $dateStr): void
    {
        $locations = $travel->getLocationsForDate($dateStr);
        $pos = 0;
        foreach ($locations as $loc) {
            foreach ($loc->getVisitDates() as $vd) {
                if ($vd->getVisitDate()->format('Y-m-d') === $dateStr) {
                    $vd->setPosition($pos++);
                    break;
                }
            }
        }
    }
}
