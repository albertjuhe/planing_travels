<?php

namespace App\Tests\Domain\Budget\Model;

use App\Domain\Budget\Model\Settlement;
use App\Tests\Domain\Travel\Model\TravelMother;
use App\Tests\Domain\User\Model\UserMother;
use PHPUnit\Framework\TestCase;

class SettlementTest extends TestCase
{
    public function testConstructorGeneratesUuidAndAssignsFields(): void
    {
        $travel = TravelMother::random();
        $from = UserMother::random();
        $to = UserMother::random();

        $settlement = new Settlement($travel, $from, $to, 47.50, 'eur', 'Settling up for dinner');

        $this->assertNotEmpty($settlement->getId());
        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
            $settlement->getId()
        );
        $this->assertSame($travel, $settlement->getTravel());
        $this->assertSame($from, $settlement->getFromUser());
        $this->assertSame($to, $settlement->getToUser());
        $this->assertSame(47.50, $settlement->getAmount());
        $this->assertSame('EUR', $settlement->getCurrency());
        $this->assertSame('Settling up for dinner', $settlement->getNote());
    }

    public function testCurrencyIsNormalizedToUppercase(): void
    {
        $settlement = SettlementMother::withCurrency(UserMother::random(), UserMother::random(), 10.0, 'usd');

        $this->assertSame('USD', $settlement->getCurrency());
    }

    public function testSettledAtIsSetToNowOnConstruction(): void
    {
        $before = new \DateTime();
        $settlement = SettlementMother::random();
        $after = new \DateTime();

        $this->assertGreaterThanOrEqual($before, $settlement->getSettledAt());
        $this->assertLessThanOrEqual($after, $settlement->getSettledAt());
    }

    public function testNoteCanBeNull(): void
    {
        $settlement = SettlementMother::random();

        $this->assertNull($settlement->getNote());
    }

    public function testToArrayReturnsExpectedStructure(): void
    {
        $from = UserMother::random();
        $from->setUsername('alice');
        $to = UserMother::random();
        $to->setUsername('bob');

        $settlement = new Settlement(TravelMother::random(), $from, $to, 25.0, 'EUR', 'lunch');

        $array = $settlement->toArray();

        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('fromUserId', $array);
        $this->assertArrayHasKey('fromUsername', $array);
        $this->assertArrayHasKey('toUserId', $array);
        $this->assertArrayHasKey('toUsername', $array);
        $this->assertArrayHasKey('amount', $array);
        $this->assertArrayHasKey('currency', $array);
        $this->assertArrayHasKey('settledAt', $array);
        $this->assertArrayHasKey('note', $array);
        $this->assertSame(25.0, $array['amount']);
        $this->assertSame('EUR', $array['currency']);
        $this->assertSame('lunch', $array['note']);
        $this->assertSame('alice', $array['fromUsername']);
        $this->assertSame('bob', $array['toUsername']);
    }

    public function testTwoSettlementsHaveDifferentIds(): void
    {
        $s1 = SettlementMother::random();
        $s2 = SettlementMother::random();

        $this->assertNotSame($s1->getId(), $s2->getId());
    }
}
