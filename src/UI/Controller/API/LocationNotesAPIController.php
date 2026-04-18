<?php

namespace App\UI\Controller\API;

use App\Domain\Note\Model\Note;
use App\Infrastructure\LocationBundle\Repository\DoctrineLocationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class LocationNotesAPIController extends AbstractController
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

    /**
     * @Route("/api/location/{locationId}/notes", name="getLocationNotes", methods={"GET"})
     */
    public function getNotes(string $locationId): JsonResponse
    {
        try {
            $location = $this->locationRepository->findById($locationId);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Location not found'], 404);
        }

        $notes = [];
        foreach ($location->getNotas() as $note) {
            $notes[] = [
                'id'      => $note->getId(),
                'content' => $note->getContent(),
            ];
        }

        return new JsonResponse(['notes' => $notes]);
    }

    /**
     * @Route("/api/location/{locationId}/notes", name="addLocationNote", methods={"POST"})
     */
    public function addNote(Request $request, string $locationId): JsonResponse
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
        if (!$this->canEdit($travel, $user)) {
            return new JsonResponse(['error' => 'Operation not allowed'], 403);
        }

        $data = json_decode($request->getContent(), true);
        $content = isset($data['content']) ? trim($data['content']) : '';

        if ($content === '') {
            return new JsonResponse(['error' => 'Content cannot be empty'], 400);
        }

        $note = new Note();
        $note->setContent($content);
        $note->setTitle('');
        $note->setLocation($location);

        $this->em->persist($note);
        $this->em->flush();

        return new JsonResponse([
            'id'      => $note->getId(),
            'content' => $note->getContent(),
        ], 201);
    }

    /**
     * @Route("/api/location/{locationId}/notes/{noteId}", name="deleteLocationNote", methods={"DELETE"})
     */
    public function deleteNote(string $locationId, int $noteId): JsonResponse
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
        if (!$this->canEdit($travel, $user)) {
            return new JsonResponse(['error' => 'Operation not allowed'], 403);
        }

        $noteToRemove = null;
        foreach ($location->getNotas() as $note) {
            if ($note->getId() === $noteId) {
                $noteToRemove = $note;
                break;
            }
        }

        if (!$noteToRemove) {
            return new JsonResponse(['error' => 'Note not found'], 404);
        }

        $this->em->remove($noteToRemove);
        $this->em->flush();

        return new JsonResponse(['success' => true]);
    }

    private function canEdit($travel, $user): bool
    {
        if ($travel->getUser()->getId()->id() === $user->getId()->id()) {
            return true;
        }
        foreach ($travel->getSharedusers() as $sharedUser) {
            if ($sharedUser->getId()->id() === $user->getId()->id()) {
                return true;
            }
        }
        return false;
    }
}
