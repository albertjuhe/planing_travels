<?php

namespace App\Application\Command\Travel;

use App\Application\Command\Command;

class CloneTravelCommand implements Command
{
    private string $sourceTravelId;
    private int $userId;
    private ?string $newTitle;
    private bool $copyGpx;

    public function __construct(
        string $sourceTravelId,
        int $userId,
        ?string $newTitle = null,
        bool $copyGpx = true
    ) {
        $this->sourceTravelId = $sourceTravelId;
        $this->userId = $userId;
        $this->newTitle = $newTitle;
        $this->copyGpx = $copyGpx;
    }

    public function getSourceTravelId(): string
    {
        return $this->sourceTravelId;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getNewTitle(): ?string
    {
        return $this->newTitle;
    }

    public function isCopyGpx(): bool
    {
        return $this->copyGpx;
    }
}
