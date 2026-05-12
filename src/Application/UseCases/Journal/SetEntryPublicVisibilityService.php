<?php

namespace App\Application\UseCases\Journal;

use App\Application\Command\Journal\SetEntryPublicVisibilityCommand;
use App\Application\UseCases\UsesCasesService;
use App\Domain\Journal\Events\JournalEntryWasMadePublic;
use App\Domain\Journal\Repository\JournalEntryRepository;
use App\Infrastructure\WebSocket\WebSocketNotifier;

class SetEntryPublicVisibilityService implements UsesCasesService
{
    private JournalEntryRepository $journalRepository;
    private WebSocketNotifier $notifier;

    public function __construct(JournalEntryRepository $journalRepository, WebSocketNotifier $notifier)
    {
        $this->journalRepository = $journalRepository;
        $this->notifier = $notifier;
    }

    public function __invoke(SetEntryPublicVisibilityCommand $command): void
    {
        $entry = $this->journalRepository->findById($command->getEntryId());
        if (!$entry) {
            throw new \RuntimeException('Journal entry not found.');
        }

        if ($entry->getAuthor()->getId()->id() !== $command->getRequesterId()) {
            throw new \RuntimeException('Not allowed to change visibility of this journal entry.');
        }

        $wasPrivate = !$entry->isPublic();
        $entry->setIsPublic($command->isPublic());
        $this->journalRepository->save($entry);

        if ($command->isPublic() && $wasPrivate) {
            $travelId = $entry->getTravel()->getId()->id();
            $this->notifier->broadcast($travelId, [
                'type' => 'journal.entry.published',
                'entryId' => $entry->getId(),
                'travelId' => $travelId,
                'entryDate' => $entry->getEntryDate()->format('Y-m-d'),
                'title' => $entry->getTitle(),
            ]);
        }
    }
}
