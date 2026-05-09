<?php

namespace App\UI\Controller\API;

use App\Domain\Location\Model\LocationVisitDate;
use App\Infrastructure\LocationBundle\Repository\DoctrineLocationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\SecurityBundle\Security;

class UpdateVisitDateTimeAPIController extends AbstractController
{
    private $locationRepository;
    private $em;
    private $security;

    public function __construct(
        DoctrineLocationRepository $locationRepository,
        EntityManagerInterface $em,
        Security $security
    ) {
        $this->locationRepository = $locationRepository;
        $this->em = $em;
        $this->security = $security;
    }

    #[Route('/api/location/{locationId}/visit-time', name: 'updateVisitTime', methods: ['PATCH'])]
    public function update(Request $request, string $locationId): JsonResponse
    {
        $user = $this->security->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], 403);
        }

        try {
            $location = $this->locationRepository->findById($locationId);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Location not found'], 404);
        }

        $travel = $location->getTravel();
        $isOwner = $travel->getUser()->getId()->id() === $user->getId()->id();
        $isShared = false;
        foreach ($travel->getSharedusers() as $shared) {
            if ($shared->getId()->id() === $user->getId()->id()) {
                $isShared = true;
                break;
            }
        }
        if (!$isOwner && !$isShared) {
            return new JsonResponse(['error' => 'Forbidden'], 403);
        }

        $data = json_decode($request->getContent(), true);
        $dateStr    = $data['date'] ?? null;
        $timeStart  = $data['timeStart'] ?? null;
        $timeEnd    = $data['timeEnd'] ?? null;

        if (!$dateStr) {
            return new JsonResponse(['error' => 'date is required'], 400);
        }

        $visitDate = null;
        foreach ($location->getVisitDates() as $vd) {
            if ($vd->getVisitDateString() === $dateStr) {
                $visitDate = $vd;
                break;
            }
        }

        if (!$visitDate) {
            return new JsonResponse(['error' => 'Visit date not found for this location'], 404);
        }

        if ($timeStart !== null) {
            $dt = $timeStart !== '' ? \DateTime::createFromFormat('H:i', $timeStart) : null;
            $visitDate->setTimeStart($dt ?: null);
        }
        if ($timeEnd !== null) {
            $dt = $timeEnd !== '' ? \DateTime::createFromFormat('H:i', $timeEnd) : null;
            $visitDate->setTimeEnd($dt ?: null);
        }

        $this->em->flush();

        return new JsonResponse([
            'success'   => true,
            'timeStart' => $visitDate->getTimeStartString(),
            'timeEnd'   => $visitDate->getTimeEndString(),
        ]);
    }
}
