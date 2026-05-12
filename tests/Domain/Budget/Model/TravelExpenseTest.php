<?php

namespace App\Tests\Domain\Budget\Model;

use App\Domain\Budget\Model\TravelExpense;
use App\Tests\Domain\Travel\Model\TravelMother;
use App\Tests\Domain\User\Model\UserMother;
use PHPUnit\Framework\TestCase;

class TravelExpenseTest extends TestCase
{
    public function testConstructorWithDefaults(): void
    {
        $travel = TravelMother::random();
        $expense = new TravelExpense($travel, 'Hotel', 120.0);

        $this->assertSame('Hotel', $expense->getDescription());
        $this->assertSame(120.0, $expense->getAmount());
        $this->assertSame('EUR', $expense->getCurrency());
        $this->assertSame(TravelExpense::CATEGORY_OTHER, $expense->getCategory());
        $this->assertSame(TravelExpense::SPLIT_EQUAL, $expense->getSplitMode());
        $this->assertSame(120.0, $expense->getAmountInTravelCurrency());
        $this->assertNull($expense->getPayer());
        $this->assertCount(0, $expense->getShares());
    }

    public function testCurrencyIsNormalizedToUppercase(): void
    {
        $expense = new TravelExpense(TravelMother::random(), 'Taxi', 20.0, TravelExpense::CATEGORY_TRANSPORT, 'usd');

        $this->assertSame('USD', $expense->getCurrency());
    }

    public function testSplitEquallyTwoParticipantsCreatesTwoShares(): void
    {
        $payer = UserMother::random();
        $other = UserMother::random();
        $expense = TravelExpenseMother::withPayer($payer, 100.0);

        $expense->splitEqually([$payer, $other], 100.0);

        $shares = $expense->getShares();
        $this->assertCount(2, $shares);
        foreach ($shares as $share) {
            $this->assertSame(50.0, $share->getAmount());
            $this->assertSame(50.0, $share->getAmountInTravelCurrency());
        }
    }

    public function testSplitEquallyThreeParticipantsRoundsCorrectly(): void
    {
        $participants = [UserMother::random(), UserMother::random(), UserMother::random()];
        $expense = TravelExpenseMother::withPayer($participants[0], 100.0);

        $expense->splitEqually($participants, 100.0);

        $shares = $expense->getShares();
        $this->assertCount(3, $shares);
        foreach ($shares as $share) {
            $this->assertSame(round(100.0 / 3, 2), $share->getAmount());
        }
    }

    public function testSplitEquallyWithEmptyParticipantsDoesNothing(): void
    {
        $expense = TravelExpenseMother::withPayer(UserMother::random(), 100.0);

        $expense->splitEqually([], 100.0);

        $this->assertCount(0, $expense->getShares());
    }

    public function testSplitExactCreatesSharesWithExactAmounts(): void
    {
        $userA = UserMother::random();
        $userB = UserMother::random();
        $idA = (string) $userA->getId()->id();
        $idB = (string) $userB->getId()->id();

        $expense = TravelExpenseMother::withPayer($userA, 100.0);
        $expense->splitExact([$idA => 30.0, $idB => 70.0], [$idA => $userA, $idB => $userB]);

        $shares = $expense->getShares();
        $this->assertCount(2, $shares);
        $amounts = array_map(fn ($s) => $s->getAmount(), $shares->toArray());
        sort($amounts);
        $this->assertSame([30.0, 70.0], $amounts);
    }

    public function testSplitExactWithExchangeRateCalculatesAmountInTravelCurrency(): void
    {
        $user = UserMother::random();
        $id = (string) $user->getId()->id();

        $expense = TravelExpenseMother::withPayer($user, 100.0);
        $expense->splitExact([$id => 100.0], [$id => $user], 1.08);

        $shares = $expense->getShares();
        $this->assertCount(1, $shares);
        $this->assertSame(round(100.0 * 1.08, 2), $shares->first()->getAmountInTravelCurrency());
    }

    public function testClearSharesEmptiesCollection(): void
    {
        $payer = UserMother::random();
        $expense = TravelExpenseMother::withPayer($payer, 100.0);
        $expense->splitEqually([$payer, UserMother::random()], 100.0);

        $this->assertCount(2, $expense->getShares());

        $expense->clearShares();

        $this->assertCount(0, $expense->getShares());
    }

    public function testAmountInTravelCurrencyFallsBackToAmountWhenZero(): void
    {
        $expense = new TravelExpense(TravelMother::random(), 'Food', 55.0);

        $this->assertSame(55.0, $expense->getAmountInTravelCurrency());
    }

    public function testSplitEquallySetsSplitModeToEqual(): void
    {
        $payer = UserMother::random();
        $expense = TravelExpenseMother::withPayer($payer, 80.0);

        $expense->splitEqually([$payer, UserMother::random()], 80.0);

        $this->assertSame(TravelExpense::SPLIT_EQUAL, $expense->getSplitMode());
    }

    public function testSplitExactSetsSplitModeToExact(): void
    {
        $user = UserMother::random();
        $id = (string) $user->getId()->id();
        $expense = TravelExpenseMother::withPayer($user, 50.0);

        $expense->splitExact([$id => 50.0], [$id => $user]);

        $this->assertSame(TravelExpense::SPLIT_EXACT, $expense->getSplitMode());
    }
}
