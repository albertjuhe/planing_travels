<?php

namespace App\Tests\ValueObject;

use App\Domain\Money\ValueObject\Currency;
use PHPUnit\Framework\TestCase;

class CurrencyTest extends TestCase
{
    public function testEqualsCurrency()
    {
        $currency1 = new Currency('EUR');
        $currency2 = new Currency('EUR');
        $this->assertTrue($currency1->equals($currency2));
        $currency2 = new Currency('USD');
        $this->assertFalse($currency1->equals($currency2));
    }

    public function testExpectExceptionInvalidArgumentTest() {
        $this->expectException(\InvalidArgumentException::class);
        $currency1 = new Currency('EU');

    }

}