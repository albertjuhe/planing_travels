<?php

namespace App\UI\Controller\API;

use App\Application\Command\Journal\AddJournalEntryCommand;
use App\Application\Command\Journal\DeleteJournalEntryCommand;
use App\Application\Command\Journal\SetEntryPublicVisibilityCommand;
use App\Application\Command\Journal\UpdateJournalEntryCommand;
use App\Application\Service\TravelAuthorizationService;
use App\Domain\Travel\Model\Travel;
use App\Infrastructure\JournalBundle\Repository\DoctrineJournalEntryRepository;
use App\Infrastructure\TravelBundle\Repository\DoctrineTravelRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Attribute\Route;

class JournalAPIController extends AbstractController
{
    private DoctrineTravelRepository $travelRepo;
    private DoctrineJournalEntryRepository $journalRepo;
    private MessageBusInterface $commandBus;
    private Security $security;
    private TravelAuthorizationService $authService;

    public function __construct(
        DoctrineTravelRepository $travelRepo,
        DoctrineJournalEntryRepository $journalRepo,
        MessageBusInterface $commandBus,
        Security $security,
        TravelAuthorizationService $authService
    ) {
        $this->travelRepo = $travelRepo;
        $this->journalRepo = $journalRepo;
        $this->commandBus = $commandBus;
        $this->security = $security;
        $this->authService = $authService;
    }

    #[Route('/api/travel/{travelId}/journal', name: 'api_journal_list', methods: ['GET'])]
    public function list(string $travelId): JsonResponse
    {
        $user = $this->security->getUser();
        try {
            $travel = $this->travelRepo->ofIdOrFail($travelId);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Travel not found'], 404);
        }

        $publicOnly = !$user || !$this->authService->canAccess($travel, $user);
        if ($publicOnly && !$travel->isPublished()) {
            return new JsonResponse(['error' => 'Forbidden'], 403);
        }

        $grouped = $this->journalRepo->findByTravelGroupedByDate($travel, $publicOnly);
        $result = [];
        foreach ($grouped as $date => $entries) {
            $result[$date] = array_map(fn ($e) => $e->toArray(), $entries);
        }

        return new JsonResponse(['journal' => $result]);
    }

    #[Route('/api/travel/{travelId}/journal', name: 'api_journal_create', methods: ['POST'])]
    public function create(Request $request, string $travelId): JsonResponse
    {
        $user = $this->security->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        $data = json_decode($request->getContent(), true) ?? [];

        if (empty($data['entryDate']) || empty($data['content'])) {
            return new JsonResponse(['error' => 'entryDate and content are required'], 400);
        }

        $command = new AddJournalEntryCommand(
            $travelId,
            $user->getId()->id(),
            $data['entryDate'],
            $data['content'],
            $data['title'] ?? null,
            $data['mood'] ?? null
        );

        try {
            $envelope = $this->commandBus->dispatch($command);
            $entry = $envelope->last(HandledStamp::class)->getResult();

            return new JsonResponse($entry->toArray(), 201);
        } catch (\RuntimeException $e) {
            return new JsonResponse(['error' => $e->getMessage()], 403);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    #[Route('/api/journal/entry/{entryId}', name: 'api_journal_update', methods: ['PUT'])]
    public function update(Request $request, string $entryId): JsonResponse
    {
        $user = $this->security->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        $data = json_decode($request->getContent(), true) ?? [];

        if (empty($data['content'])) {
            return new JsonResponse(['error' => 'content is required'], 400);
        }

        $command = new UpdateJournalEntryCommand(
            $entryId,
            $user->getId()->id(),
            $data['content'],
            $data['title'] ?? null,
            $data['mood'] ?? null
        );

        try {
            $this->commandBus->dispatch($command);

            return new JsonResponse(['success' => true]);
        } catch (\RuntimeException $e) {
            return new JsonResponse(['error' => $e->getMessage()], 403);
        }
    }

    #[Route('/api/journal/entry/{entryId}', name: 'api_journal_delete', methods: ['DELETE'])]
    public function delete(string $entryId): JsonResponse
    {
        $user = $this->security->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        $command = new DeleteJournalEntryCommand($entryId, $user->getId()->id());

        try {
            $this->commandBus->dispatch($command);

            return new JsonResponse(['success' => true]);
        } catch (\RuntimeException $e) {
            return new JsonResponse(['error' => $e->getMessage()], 403);
        }
    }

    #[Route('/api/journal/entry/{entryId}/visibility', name: 'api_journal_visibility', methods: ['PUT'])]
    public function visibility(Request $request, string $entryId): JsonResponse
    {
        $user = $this->security->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        $data = json_decode($request->getContent(), true) ?? [];
        $isPublic = (bool) ($data['isPublic'] ?? false);

        $command = new SetEntryPublicVisibilityCommand($entryId, $user->getId()->id(), $isPublic);

        try {
            $this->commandBus->dispatch($command);

            return new JsonResponse(['success' => true, 'isPublic' => $isPublic]);
        } catch (\RuntimeException $e) {
            return new JsonResponse(['error' => $e->getMessage()], 403);
        }
    }
}
