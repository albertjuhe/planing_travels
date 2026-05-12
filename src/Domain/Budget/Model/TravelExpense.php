<?php

namespace App\Domain\Budget\Model;

use App\Domain\Location\Model\Location;
use App\Domain\Travel\Model\Travel;
use App\Domain\User\Model\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

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

    public const SPLIT_EQUAL = 'equal';
    public const SPLIT_EXACT = 'exact';

    /** @var int */
    private $id;

    /** @var Travel */
    private $travel;

    /** @var User|null */
    private $payer;

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

    /** @var string */
    private $splitMode;

    /** @var float */
    private $amountInTravelCurrency;

    /** @var float */
    private $exchangeRateAtCreation;

    /** @var \DateTime|null */
    private $expenseDate;

    /** @var \DateTime */
    private $createdAt;

    /** @var \DateTime */
    private $updatedAt;

    /** @var Collection|ExpenseShare[] */
    private $shares;

    public function __construct(
        Travel $travel,
        string $description,
        float $amount,
        string $category = self::CATEGORY_OTHER,
        string $currency = 'EUR',
        ?Location $location = null,
        ?\DateTime $expenseDate = null,
        ?User $payer = null,
        string $splitMode = self::SPLIT_EQUAL,
        float $amountInTravelCurrency = 0.0,
        float $exchangeRateAtCreation = 1.0
    ) {
        $this->travel = $travel;
        $this->description = $description;
        $this->amount = $amount;
        $this->category = $category;
        $this->currency = strtoupper($currency);
        $this->location = $location;
        $this->expenseDate = $expenseDate;
        $this->payer = $payer;
        $this->splitMode = $splitMode;
        $this->amountInTravelCurrency = $amountInTravelCurrency > 0 ? $amountInTravelCurrency : $amount;
        $this->exchangeRateAtCreation = $exchangeRateAtCreation;
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->shares = new ArrayCollection();
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

    public function getPayer(): ?User
    {
        return $this->payer;
    }

    public function setPayer(?User $payer): void
    {
        $this->payer = $payer;
        $this->updatedAt = new \DateTime();
    }

    public function getSplitMode(): string
    {
        return $this->splitMode ?? self::SPLIT_EQUAL;
    }

    public function setSplitMode(string $splitMode): void
    {
        $this->splitMode = $splitMode;
        $this->updatedAt = new \DateTime();
    }

    public function getAmountInTravelCurrency(): float
    {
        return $this->amountInTravelCurrency ?? $this->amount;
    }

    public function setAmountInTravelCurrency(float $amountInTravelCurrency): void
    {
        $this->amountInTravelCurrency = $amountInTravelCurrency;
    }

    public function getExchangeRateAtCreation(): float
    {
        return $this->exchangeRateAtCreation ?? 1.0;
    }

    public function setExchangeRateAtCreation(float $rate): void
    {
        $this->exchangeRateAtCreation = $rate;
    }

    public function getShares(): Collection
    {
        return $this->shares ?? new ArrayCollection();
    }

    public function addShare(ExpenseShare $share): void
    {
        if (!$this->shares->contains($share)) {
            $this->shares->add($share);
        }
    }

    public function clearShares(): void
    {
        $this->shares->clear();
    }

    public function splitEqually(array $participants, float $amountInTravelCurrency): void
    {
        if (empty($participants)) {
            return;
        }
        $this->clearShares();
        $this->splitMode = self::SPLIT_EQUAL;
        $count = count($participants);
        $shareAmount = round($this->amount / $count, 2);
        $shareTravelAmount = round($amountInTravelCurrency / $count, 2);
        foreach ($participants as $participant) {
            $share = new ExpenseShare($this, $participant, $shareAmount, $shareTravelAmount);
            $this->addShare($share);
        }
    }

    public function splitExact(array $userIdToAmount, array $usersById, float $exchangeRate = 1.0): void
    {
        $this->clearShares();
        $this->splitMode = self::SPLIT_EXACT;
        foreach ($userIdToAmount as $userId => $amount) {
            if (!isset($usersById[$userId])) {
                continue;
            }
            $share = new ExpenseShare(
                $this,
                $usersById[$userId],
                (float) $amount,
                round((float) $amount * $exchangeRate, 2)
            );
            $this->addShare($share);
        }
    }
}
