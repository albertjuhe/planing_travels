<?php

namespace App\Domain\Money\ValueObject;

class Currency
{
    private $isoCode;

    public function __construct($anIsoCode)
    {
        $this->setIsoCode($anIsoCode);
    }

    private function setIsoCode($anIsoCode)
    {
        if (!preg_match('/^[A-Z]{3}$/', $anIsoCode)) {
            throw new \InvalidArgumentException();
        }
        $this->isoCode = $anIsoCode;
    }

    public function isoCode()
    {
        return $this->isoCode;
    }

    public function equals(Currency $currency)
    {
        return $this->isoCode() === $currency->isoCode();
    }
}
