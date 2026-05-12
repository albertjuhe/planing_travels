<?php

namespace App\Application\Command\Journal;

use App\Application\Command\Command;

class UpdateJournalEntryCommand implements Command
{
    private string $entryId;
    private int $authorId;
    private string $content;
    private ?string $title;
    private ?string $mood;

    public function __construct(
        string $entryId,
        int $authorId,
        string $content,
        ?string $title = null,
        ?string $mood = null
    ) {
        $this->entryId = $entryId;
        $this->authorId = $authorId;
        $this->content = $content;
        $this->title = $title;
        $this->mood = $mood;
    }

    public function getEntryId(): string { return $this->entryId; }
    public function getAuthorId(): int { return $this->authorId; }
    public function getContent(): string { return $this->content; }
    public function getTitle(): ?string { return $this->title; }
    public function getMood(): ?string { return $this->mood; }
}
