<?php

namespace App\Domain\Money\Service;

interface ExchangeRateProvider
{
    /**
     * Returns the exchange rate from $from to $to for the given date (or today if null).
     * Returns 1.0 if from === to.
     *
     * @throws \RuntimeException if rate cannot be fetched
     */
    public function getRate(string $from, string $to, ?\DateTime $date = null): float;
}
