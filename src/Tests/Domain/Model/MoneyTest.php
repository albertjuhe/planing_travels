<?php
/**
 * Created by PhpStorm.
 * User: ajuhe
 * Date: 8/07/18
 * Time: 18:47
 */

namespace App\Tests\Domain\Model;

use App\Domain\Model\Money\ValueObject\Money;
use App\Domain\Model\Money\ValueObject\Currency;
use PHPUnit\Framework\TestCase;

class MoneyTest extends TestCase
{
    public function testFromMOney() {
        $money = new Money(150,new Currency('USD'));
        $newMoney = Money::fromMoney($money);
        $this->assertTrue($money->equal($newMoney));
    }

    public function testOfCurrency() {
        $money = new Money(0,new Currency('USD'));
        $newMoney = Money::ofCurrency(new Currency('USD'));
        $this->assertTrue($money->equal($newMoney));
    }

    public function testIncreaseAmountBy()
    {
        $currency = new Currency('EUR');
        $money = new Money(300,$currency);
        $newMoney = $money->increaseAmountBy(150);

        $this->assertEquals(450,$newMoney->amount());
    }

    public function testAdd() {
        $currency = new Currency('EUR');
        $money = new Money(300,$currency);
        $money2 = new Money(200,$currency);
        $newMoney = $money->add($money2);
        $this->assertEquals(500,$newMoney->amount());
    }
}