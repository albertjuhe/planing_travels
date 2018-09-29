<?php
/**
 * Created by PhpStorm.
 * User: ajuhe
 * Date: 8/07/18
 * Time: 18:47
 */

namespace App\Tests\Domain\ValueObject;

use App\Domain\Money\ValueObject\Money;
use App\Domain\Money\ValueObject\Currency;
use PHPUnit\Framework\TestCase;

class MoneyTest extends TestCase
{

    public function testFromMoney()
    {
        $money = new Money(150, new Currency('USD'));
        $newMoney = Money::fromMoney($money);
        $this->assertTrue($money->equal($newMoney));
    }

    public function testOfCurrency()
    {
        $money = new Money(0, new Currency('USD'));
        $newMoney = Money::ofCurrency(new Currency('USD'));
        $this->assertTrue($money->equal($newMoney));
    }

    public function testIncreaseAmountBy()
    {
        $currency = new Currency('EUR');
        $money = new Money(300, $currency);
        $newMoney = $money->increaseAmountBy(150);

        $this->assertEquals(450, $newMoney->amount());
    }

    public function testAdd()
    {
        $currency = new Currency('EUR');
        $money = new Money(300, $currency);
        $money2 = new Money(200, $currency);
        $newMoney = $money->add($money2);
        $this->assertEquals(500, $newMoney->amount());
    }

    public function testEqualMoney()
    {
        $currency = new Currency('EUR');
        $money = new Money(300, $currency);
        $money2 = new Money(300, $currency);
        $this->assertTrue($money->equal($money2));

        $currency = new Currency('USD');
        $money3 = new Money(300, $currency);
        $this->assertFalse($money->equal($money3));

        $money4 = new Money(100, $currency);
        $this->assertFalse($money->equal($money4));
    }

    public function testAmount()
    {
        $currency = new Currency('USD');
        $money = new Money(100, $currency);
        $this->assertEquals(100,$money->amount());
    }
}