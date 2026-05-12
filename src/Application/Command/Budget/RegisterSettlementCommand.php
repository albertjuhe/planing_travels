<?php

namespace App\Application\Command\Budget;

use App\Application\Command\Command;

class RegisterSettlementCommand implements Command
{
    private string $travelId;
    private int $fromUserId;
    private int $toUserId;
    private float $amount;
    private string $currency;
    private ?string $note;

    public function __construct(
        string $travelId,
        int $fromUserId,
        int $toUserId,
        float $amount,
        string $currency,
        ?string $note = null
    ) {
        $this->travelId = $travelId;
        $this->fromUserId = $fromUserId;
        $this->toUserId = $toUserId;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->note = $note;
    }

    public function getTravelId(): string { return $this->travelId; }
    public function getFromUserId(): int { return $this->fromUserId; }
    public function getToUserId(): int { return $this->toUserId; }
    public function getAmount(): float { return $this->amount; }
    public function getCurrency(): string { return $this->currency; }
    public function getNote(): ?string { return $this->note; }
}
