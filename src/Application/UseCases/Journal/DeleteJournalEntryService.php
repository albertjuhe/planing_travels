<?php

namespace App\Application\UseCases\Journal;

use App\Application\Command\Journal\DeleteJournalEntryCommand;
use App\Application\UseCases\UsesCasesService;
use App\Domain\Journal\Repository\JournalEntryRepository;

class DeleteJournalEntryService implements UsesCasesService
{
    private JournalEntryRepository $journalRepository;

    public function __construct(JournalEntryRepository $journalRepository)
    {
        $this->journalRepository = $journalRepository;
    }

    public function __invoke(DeleteJournalEntryCommand $command): void
    {
        $entry = $this->journalRepository->findById($command->getEntryId());
        if (!$entry) {
            throw new \RuntimeException('Journal entry not found.');
        }

        if ($entry->getAuthor()->getId()->id() !== $command->getRequesterId()) {
            throw new \RuntimeException('Not allowed to delete this journal entry.');
        }

        $this->journalRepository->remove($entry);
    }
}
