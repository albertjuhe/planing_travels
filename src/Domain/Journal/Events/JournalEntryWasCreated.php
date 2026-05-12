<?php

namespace App\Domain\Journal\Events;

use App\Domain\Event\DomainEvent;

class JournalEntryWasCreated implements DomainEvent
{
    private string $entryId;
    private string $travelId;
    private int $authorId;
    private string $entryDate;
    private \DateTime $occurredOn;

    public function __construct(string $entryId, string $travelId, int $authorId, string $entryDate)
    {
        $this->entryId = $entryId;
        $this->travelId = $travelId;
        $this->authorId = $authorId;
        $this->entryDate = $entryDate;
        $this->occurredOn = new \DateTime();
    }

    public function getEntryId(): string { return $this->entryId; }
    public function getTravelId(): string { return $this->travelId; }
    public function getAuthorId(): int { return $this->authorId; }
    public function getEntryDate(): string { return $this->entryDate; }
    public function occurredOn(): \DateTime { return $this->occurredOn; }
}
