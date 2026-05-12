<?php

namespace App\Application\Command\Journal;

use App\Application\Command\Command;

class SetEntryPublicVisibilityCommand implements Command
{
    private string $entryId;
    private int $requesterId;
    private bool $isPublic;

    public function __construct(string $entryId, int $requesterId, bool $isPublic)
    {
        $this->entryId = $entryId;
        $this->requesterId = $requesterId;
        $this->isPublic = $isPublic;
    }

    public function getEntryId(): string { return $this->entryId; }
    public function getRequesterId(): int { return $this->requesterId; }
    public function isPublic(): bool { return $this->isPublic; }
}
