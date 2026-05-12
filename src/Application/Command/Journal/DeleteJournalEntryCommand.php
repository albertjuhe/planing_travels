<?php

namespace App\Application\Command\Journal;

use App\Application\Command\Command;

class DeleteJournalEntryCommand implements Command
{
    private string $entryId;
    private int $requesterId;

    public function __construct(string $entryId, int $requesterId)
    {
        $this->entryId = $entryId;
        $this->requesterId = $requesterId;
    }

    public function getEntryId(): string { return $this->entryId; }
    public function getRequesterId(): int { return $this->requesterId; }
}
