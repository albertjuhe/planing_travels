<?php

namespace App\Domain\Budget\Model;

use App\Domain\Location\Model\Location;
use App\Domain\Travel\Model\Travel;

class TravelExpense
{
    public const CATEGORY_ACCOMMODATION = 'accommodation';
    public const CATEGORY_TRANSPORT     = 'transport';
    public const CATEGORY_FOOD          = 'food';
    public const CATEGORY_ACTIVITIES    = 'activities';
    public const CATEGORY_SHOPPING      = 'shopping';
    public const CATEGORY_OTHER         = 'other';

    public const CATEGORIES = [
        self::CATEGORY_ACCOMMODATION => 'Accommodation',
        self::CATEGORY_TRANSPORT     => 'Transport',
        self::CATEGORY_FOOD          => 'Food & Drink',
        self::CATEGORY_ACTIVITIES    => 'Activities',
        self::CATEGORY_SHOPPING      => 'Shopping',
        self::CATEGORY_OTHER         => 'Other',
    ];

    /** @var int */
    private $id;

    /** @var Travel */
    private $travel;

    /** @var Location|null */
    private $location;

    /** @var string */
    private $description;

    /** @var float */
    private $amount;

    /** @var string */
    private $currency;

    /** @var string */
    private $category;

    /** @var \DateTime|null */
    private $expenseDate;

    /** @var \DateTime */
    private $createdAt;

    /** @var \DateTime */
    private $updatedAt;

    public function __construct(
        Travel $travel,
        string $description,
        float $amount,
        string $category = self::CATEGORY_OTHER,
        string $currency = 'EUR',
        ?Location $location = null,
        ?\DateTime $expenseDate = null
    ) {
        $this->travel = $travel;
        $this->description = $description;
        $this->amount = $amount;
        $this->category = $category;
        $this->currency = strtoupper($currency);
        $this->location = $location;
        $this->expenseDate = $expenseDate;
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

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setLocation(?Location $location): void
    {
        $this->location = $location;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
        $this->updatedAt = new \DateTime();
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

    public function getCategory(): string
    {
        return $this->category;
    }

    public function setCategory(string $category): void
    {
        $this->category = $category;
        $this->updatedAt = new \DateTime();
    }

    public function getExpenseDate(): ?\DateTime
    {
        return $this->expenseDate;
    }

    public function setExpenseDate(?\DateTime $expenseDate): void
    {
        $this->expenseDate = $expenseDate;
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
