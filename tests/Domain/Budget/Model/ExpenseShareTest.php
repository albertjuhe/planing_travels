<?php

namespace App\Tests\Domain\Budget\Model;

use App\Domain\Budget\Model\ExpenseShare;
use App\Domain\Budget\Model\TravelExpense;
use App\Tests\Domain\Travel\Model\TravelMother;
use App\Tests\Domain\User\Model\UserMother;
use PHPUnit\Framework\TestCase;

class ExpenseShareTest extends TestCase
{
    private function buildExpense(): TravelExpense
    {
        $payer = UserMother::random();

        return TravelExpenseMother::withPayer($payer, 90.0, TravelMother::random());
    }

    public function testConstructorAssignsAllFields(): void
    {
        $expense = $this->buildExpense();
        $debtor = UserMother::random();

        $share = new ExpenseShare($expense, $debtor, 30.0, 28.5);

        $this->assertSame($expense, $share->getExpense());
        $this->assertSame($debtor, $share->getDebtor());
        $this->assertSame(30.0, $share->getAmount());
        $this->assertSame(28.5, $share->getAmountInTravelCurrency());
    }

    public function testIsNotSettledByDefault(): void
    {
        $share = ExpenseShareMother::forDebtor(UserMother::random(), 50.0, $this->buildExpense());

        $this->assertFalse($share->isSettled());
        $this->assertNull($share->getSettledAt());
    }

    public function testMarkSettledSetsSettledAtAndIsSettled(): void
    {
        $share = ExpenseShareMother::forDebtor(UserMother::random(), 50.0, $this->buildExpense());

        $share->markSettled();

        $this->assertTrue($share->isSettled());
        $this->assertInstanceOf(\DateTime::class, $share->getSettledAt());
    }

    public function testToArrayReturnsExpectedStructure(): void
    {
        $debtor = UserMother::random();
        $debtor->setUsername('alice');
        $share = new ExpenseShare($this->buildExpense(), $debtor, 30.0, 28.5);

        $array = $share->toArray();

        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('debtorId', $array);
        $this->assertArrayHasKey('debtorUsername', $array);
        $this->assertArrayHasKey('amount', $array);
        $this->assertArrayHasKey('amountInTravelCurrency', $array);
        $this->assertArrayHasKey('settledAt', $array);
        $this->assertArrayHasKey('isSettled', $array);
        $this->assertSame(30.0, $array['amount']);
        $this->assertSame(28.5, $array['amountInTravelCurrency']);
        $this->assertFalse($array['isSettled']);
        $this->assertNull($array['settledAt']);
    }

    public function testToArrayAfterSettledShowsSettledAt(): void
    {
        $share = ExpenseShareMother::forDebtor(UserMother::random(), 50.0, $this->buildExpense());
        $share->markSettled();

        $array = $share->toArray();

        $this->assertTrue($array['isSettled']);
        $this->assertNotNull($array['settledAt']);
    }
}
