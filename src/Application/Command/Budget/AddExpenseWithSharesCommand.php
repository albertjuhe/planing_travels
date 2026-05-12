<?php

namespace App\Application\Command\Budget;

use App\Application\Command\Command;

class AddExpenseWithSharesCommand implements Command
{
    private string $travelId;
    private int $requesterId;
    private string $description;
    private float $amount;
    private string $currency;
    private string $category;
    private int $payerUserId;
    private string $splitMode;
    /** @var int[] participant user IDs (for EQUAL) */
    private array $participantIds;
    /** @var array<int, float> userId => amount (for EXACT) */
    private array $exactShares;
    private ?string $expenseDate;
    private ?string $locationId;

    public function __construct(
        string $travelId,
        int $requesterId,
        string $description,
        float $amount,
        string $currency,
        string $category,
        int $payerUserId,
        string $splitMode,
        array $participantIds = [],
        array $exactShares = [],
        ?string $expenseDate = null,
        ?string $locationId = null
    ) {
        $this->travelId = $travelId;
        $this->requesterId = $requesterId;
        $this->description = $description;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->category = $category;
        $this->payerUserId = $payerUserId;
        $this->splitMode = $splitMode;
        $this->participantIds = $participantIds;
        $this->exactShares = $exactShares;
        $this->expenseDate = $expenseDate;
        $this->locationId = $locationId;
    }

    public function getTravelId(): string { return $this->travelId; }
    public function getRequesterId(): int { return $this->requesterId; }
    public function getDescription(): string { return $this->description; }
    public function getAmount(): float { return $this->amount; }
    public function getCurrency(): string { return $this->currency; }
    public function getCategory(): string { return $this->category; }
    public function getPayerUserId(): int { return $this->payerUserId; }
    public function getSplitMode(): string { return $this->splitMode; }
    public function getParticipantIds(): array { return $this->participantIds; }
    public function getExactShares(): array { return $this->exactShares; }
    public function getExpenseDate(): ?string { return $this->expenseDate; }
    public function getLocationId(): ?string { return $this->locationId; }
}
