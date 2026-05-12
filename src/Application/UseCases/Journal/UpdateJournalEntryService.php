<?php

namespace App\Application\UseCases\Journal;

use App\Application\Command\Journal\UpdateJournalEntryCommand;
use App\Application\UseCases\UsesCasesService;
use App\Domain\Journal\Repository\JournalEntryRepository;

class UpdateJournalEntryService implements UsesCasesService
{
    private JournalEntryRepository $journalRepository;

    public function __construct(JournalEntryRepository $journalRepository)
    {
        $this->journalRepository = $journalRepository;
    }

    public function __invoke(UpdateJournalEntryCommand $command): void
    {
        $entry = $this->journalRepository->findById($command->getEntryId());
        if (!$entry) {
            throw new \RuntimeException('Journal entry not found.');
        }

        if ($entry->getAuthor()->getId()->id() !== $command->getAuthorId()) {
            throw new \RuntimeException('Not allowed to edit this journal entry.');
        }

        $entry->setContent($command->getContent());
        $entry->setTitle($command->getTitle());
        if ($command->getMood() !== null) {
            $entry->setMood($command->getMood());
        }

        $this->journalRepository->save($entry);
    }
}
