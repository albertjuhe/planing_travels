<?php

namespace App\Domain\Journal\Events;

use App\Domain\Event\DomainEvent;

class JournalEntryWasMadePublic implements DomainEvent
{
    private string $entryId;
    private string $travelId;
    private \DateTime $occurredOn;

    public function __construct(string $entryId, string $travelId)
    {
        $this->entryId = $entryId;
        $this->travelId = $travelId;
        $this->occurredOn = new \DateTime();
    }

    public function getEntryId(): string { return $this->entryId; }
    public function getTravelId(): string { return $this->travelId; }
    public function occurredOn(): \DateTime { return $this->occurredOn; }
}
