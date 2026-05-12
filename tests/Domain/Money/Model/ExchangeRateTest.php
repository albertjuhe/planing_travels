<?php

namespace App\Tests\Domain\Money\Model;

use App\Domain\Money\Model\ExchangeRate;
use PHPUnit\Framework\TestCase;

class ExchangeRateTest extends TestCase
{
    public function testConstructorNormalizesCurrenciesToUppercase(): void
    {
        $rate = new ExchangeRate('eur', 'usd', 1.08, new \DateTime('today'));

        $this->assertSame('EUR', $rate->getFromCurrency());
        $this->assertSame('USD', $rate->getToCurrency());
    }

    public function testConstructorSetsFetchedAtToNow(): void
    {
        $before = new \DateTime();
        $rate = new ExchangeRate('EUR', 'USD', 1.08, new \DateTime('today'));
        $after = new \DateTime();

        $this->assertGreaterThanOrEqual($before, $rate->getFetchedAt());
        $this->assertLessThanOrEqual($after, $rate->getFetchedAt());
    }

    public function testGettersReturnCorrectValues(): void
    {
        $date = new \DateTime('2024-06-15');
        $rate = new ExchangeRate('GBP', 'JPY', 190.5, $date);

        $this->assertSame('GBP', $rate->getFromCurrency());
        $this->assertSame('JPY', $rate->getToCurrency());
        $this->assertSame(190.5, $rate->getRate());
        $this->assertSame($date, $rate->getValidForDate());
    }

    public function testIsNotStaleWhenFreshlyCreated(): void
    {
        $rate = new ExchangeRate('EUR', 'USD', 1.08, new \DateTime('today'));

        $this->assertFalse($rate->isStale(24));
    }

    public function testIsStaleAfterTtlExpired(): void
    {
        $rate = new ExchangeRate('EUR', 'USD', 1.08, new \DateTime('today'));

        $this->assertTrue($rate->isStale(-1));
    }

    public function testSetRateUpdatesFetchedAt(): void
    {
        $rate = new ExchangeRate('EUR', 'USD', 1.05, new \DateTime('today'));
        $before = clone $rate->getFetchedAt();
        usleep(1000);

        $rate->setRate(1.08);

        $this->assertSame(1.08, $rate->getRate());
        $this->assertGreaterThanOrEqual($before, $rate->getFetchedAt());
    }
}
