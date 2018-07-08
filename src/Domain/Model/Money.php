<?php
/**
 * Created by PhpStorm.
 * User: ajuhe
 * Date: 8/07/18
 * Time: 11:32
 */

namespace App\Domain\Model;


class Money
{
    private $amount;
    private $currency;

    public function __construct($anAmount, Currency $aCurrency)
    {
        $this->setAmount($anAmount);
        $this->setCurrency($aCurrency);
    }
    private function setAmount($anAmount)
    {
        $this->amount = (int) $anAmount;
    }
    private function setCurrency(Currency $aCurrency)
    {
        $this->currency = $aCurrency;
    }
    public function amount()
    {
        return $this->amount;
    }
    public function currency()
    {
        return $this->currency;
    }

    /**
     * Adding ammount to the money
     * @param integer $amount
     * @return Money
     */
    public function increaseAmountBy(integer $amount) {
        return new self(
            $this->amount() + $amount,
            $this->currency()
        );
    }
}