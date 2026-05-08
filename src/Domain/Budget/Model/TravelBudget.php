<?php

namespace App\Domain\Budget\Model;

use App\Domain\Travel\Model\Travel;

class TravelBudget
{
    /** @var int */
    private $id;

    /** @var Travel */
    private $travel;

    /** @var float */
    private $amount;

    /** @var string */
    private $currency;

    /** @var \DateTime */
    private $createdAt;

    /** @var \DateTime */
    private $updatedAt;

    public function __construct(Travel $travel, float $amount, string $currency = 'EUR')
    {
        $this->travel = $travel;
        $this->amount = $amount;
        $this->currency = strtoupper($currency);
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTravel(): Travel
    {
        return $this->travel;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
        $this->updatedAt = new \DateTime();
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): void
    {
        $this->currency = strtoupper($currency);
        $this->updatedAt = new \DateTime();
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }
}
