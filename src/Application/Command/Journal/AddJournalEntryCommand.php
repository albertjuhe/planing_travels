<?php

namespace App\Application\Command\Journal;

use App\Application\Command\Command;

class AddJournalEntryCommand implements Command
{
    private string $travelId;
    private int $authorId;
    private string $entryDate;
    private string $content;
    private ?string $title;
    private ?string $mood;

    public function __construct(
        string $travelId,
        int $authorId,
        string $entryDate,
        string $content,
        ?string $title = null,
        ?string $mood = null
    ) {
        $this->travelId = $travelId;
        $this->authorId = $authorId;
        $this->entryDate = $entryDate;
        $this->content = $content;
        $this->title = $title;
        $this->mood = $mood;
    }

    public function getTravelId(): string { return $this->travelId; }
    public function getAuthorId(): int { return $this->authorId; }
    public function getEntryDate(): string { return $this->entryDate; }
    public function getContent(): string { return $this->content; }
    public function getTitle(): ?string { return $this->title; }
    public function getMood(): ?string { return $this->mood; }
}
