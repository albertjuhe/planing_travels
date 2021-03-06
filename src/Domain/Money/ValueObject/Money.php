<?php

namespace App\Domain\Money\ValueObject;

/**
 * ValueObject MOney
 * Class Money.
 */
class Money
{
    private $amount;
    private $currency;

    public function __construct($anAmount, Currency $aCurrency)
    {
        $this->setAmount($anAmount);
        $this->setCurrency($aCurrency);
    }

    public static function fromMoney(Money $aMoney)
    {
        return new self(
            $aMoney->amount(),
            $aMoney->currency()
        );
    }

    public static function ofCurrency(Currency $aCurrency)
    {
        return new self(0, $aCurrency);
    }

    private function setAmount($anAmount)
    {
        $this->amount = (int) $anAmount;
    }

    private function setCurrency(Currency $aCurrency)
    {
        $this->currency = $aCurrency;
    }

    public function amount(): int
    {
        return $this->amount;
    }

    public function currency(): Currency
    {
        return $this->currency;
    }

    /**
     * Adding ammount to the money.
     *
     * @param int $amount
     *
     * @return Money
     */
    public function increaseAmountBy(int $amount): Money
    {
        return new self(
            $this->amount() + $amount,
            $this->currency()
        );
    }

    public function equal(Money $money): bool
    {
        return
            $this->amount() === $money->amount() &&
            $this->currency()->equals($money->currency());
    }

    public function add(Money $money): Money
    {
        if (!$this->currency()->equals($money->currency())) {
            throw new \InvalidArgumentException();
        }

        return new self(
            $this->amount() + $money->amount(),
            $this->currency()
        );
    }
}
