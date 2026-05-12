<?php

namespace App\Domain\Money\Model;

class ExchangeRate
{
    /** @var int */
    private $id;

    /** @var string */
    private $fromCurrency;

    /** @var string */
    private $toCurrency;

    /** @var float */
    private $rate;

    /** @var \DateTime */
    private $fetchedAt;

    /** @var \DateTime */
    private $validForDate;

    public function __construct(
        string $fromCurrency,
        string $toCurrency,
        float $rate,
        \DateTime $validForDate
    ) {
        $this->fromCurrency = strtoupper($fromCurrency);
        $this->toCurrency = strtoupper($toCurrency);
        $this->rate = $rate;
        $this->validForDate = $validForDate;
        $this->fetchedAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFromCurrency(): string
    {
        return $this->fromCurrency;
    }

    public function getToCurrency(): string
    {
        return $this->toCurrency;
    }

    public function getRate(): float
    {
        return $this->rate;
    }

    public function setRate(float $rate): void
    {
        $this->rate = $rate;
        $this->fetchedAt = new \DateTime();
    }

    public function getFetchedAt(): \DateTime
    {
        return $this->fetchedAt;
    }

    public function getValidForDate(): \DateTime
    {
        return $this->validForDate;
    }

    public function isStale(int $ttlHours = 24): bool
    {
        $diff = (new \DateTime())->getTimestamp() - $this->fetchedAt->getTimestamp();

        return $diff > $ttlHours * 3600;
    }
}
