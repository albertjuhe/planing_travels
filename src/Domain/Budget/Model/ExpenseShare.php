<?php

namespace App\Domain\Budget\Model;

use App\Domain\User\Model\User;

class ExpenseShare
{
    /** @var int */
    private $id;

    /** @var TravelExpense */
    private $expense;

    /** @var User */
    private $debtor;

    /** @var float */
    private $amount;

    /** @var float */
    private $amountInTravelCurrency;

    /** @var \DateTime|null */
    private $settledAt;

    public function __construct(
        TravelExpense $expense,
        User $debtor,
        float $amount,
        float $amountInTravelCurrency
    ) {
        $this->expense = $expense;
        $this->debtor = $debtor;
        $this->amount = $amount;
        $this->amountInTravelCurrency = $amountInTravelCurrency;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getExpense(): TravelExpense
    {
        return $this->expense;
    }

    public function getDebtor(): User
    {
        return $this->debtor;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getAmountInTravelCurrency(): float
    {
        return $this->amountInTravelCurrency;
    }

    public function getSettledAt(): ?\DateTime
    {
        return $this->settledAt;
    }

    public function markSettled(): void
    {
        $this->settledAt = new \DateTime();
    }

    public function isSettled(): bool
    {
        return $this->settledAt !== null;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'debtorId' => $this->debtor->getId()->id(),
            'debtorUsername' => $this->debtor->getUsername(),
            'amount' => $this->amount,
            'amountInTravelCurrency' => $this->amountInTravelCurrency,
            'settledAt' => $this->settledAt ? $this->settledAt->format('Y-m-d H:i') : null,
            'isSettled' => $this->isSettled(),
        ];
    }
}
