<?php

namespace App\Tests\Domain\Budget\Service;

use App\Domain\Budget\Model\TravelExpense;
use App\Domain\Budget\Service\BalanceCalculator;
use App\Tests\Domain\Budget\Model\ExpenseShareMother;
use App\Tests\Domain\Budget\Model\SettlementMother;
use App\Tests\Domain\Budget\Model\TravelExpenseMother;
use App\Tests\Domain\Travel\Model\TravelMother;
use App\Tests\Domain\User\Model\UserMother;
use PHPUnit\Framework\TestCase;

class BalanceCalculatorTest extends TestCase
{
    private BalanceCalculator $calculator;

    protected function setUp(): void
    {
        $this->calculator = new BalanceCalculator();
    }

    private function buildExpenseWithShares(array $users, float $totalAmount, int $payerIndex, ?TravelExpense &$expenseRef = null): TravelExpense
    {
        $travel = TravelMother::random();
        $payer = $users[$payerIndex];
        $expense = TravelExpenseMother::withPayer($payer, $totalAmount, $travel);
        $expense->splitEqually($users, $totalAmount);
        $expenseRef = $expense;

        return $expense;
    }

    private function indexBalances(array $balances): array
    {
        $indexed = [];
        foreach ($balances as $b) {
            $indexed[$b->userId] = $b->netBalance;
        }

        return $indexed;
    }

    public function testNoExpensesNoSettlementsReturnsEmpty(): void
    {
        $result = $this->calculator->calculate([], []);

        $this->assertSame([], $result['balances']);
        $this->assertSame([], $result['transfers']);
    }

    public function testTwoPersonsSimpleDebt(): void
    {
        $userA = UserMother::random();
        $userB = UserMother::random();
        $idA = (string) $userA->getId()->id();
        $idB = (string) $userB->getId()->id();

        $expense = TravelExpenseMother::withPayer($userA, 100.0);
        $expense->splitEqually([$userA, $userB], 100.0);

        $result = $this->calculator->calculate([$expense], []);
        $balances = $this->indexBalances($result['balances']);

        $this->assertSame(50.0, $balances[$idA]);
        $this->assertSame(-50.0, $balances[$idB]);

        $this->assertCount(1, $result['transfers']);
        $transfer = $result['transfers'][0];
        $this->assertSame($idB, $transfer->fromUserId);
        $this->assertSame($idA, $transfer->toUserId);
        $this->assertSame(50.0, $transfer->amount);
    }

    public function testThreePersonsEqualSplitTwoTransfers(): void
    {
        $userA = UserMother::random();
        $userB = UserMother::random();
        $userC = UserMother::random();
        $idA = (string) $userA->getId()->id();
        $idB = (string) $userB->getId()->id();
        $idC = (string) $userC->getId()->id();

        $expense = TravelExpenseMother::withPayer($userA, 90.0);
        $expense->splitEqually([$userA, $userB, $userC], 90.0);

        $result = $this->calculator->calculate([$expense], []);
        $balances = $this->indexBalances($result['balances']);

        $this->assertSame(60.0, $balances[$idA]);
        $this->assertSame(-30.0, $balances[$idB]);
        $this->assertSame(-30.0, $balances[$idC]);

        $this->assertCount(2, $result['transfers']);
        $transferAmounts = array_map(fn ($t) => $t->amount, $result['transfers']);
        foreach ($transferAmounts as $amount) {
            $this->assertSame(30.0, $amount);
        }
    }

    public function testCircularDebtsSimplifyToZeroTransfers(): void
    {
        $userA = UserMother::random();
        $userB = UserMother::random();
        $userC = UserMother::random();

        // A pays 30 for B
        $exp1 = TravelExpenseMother::withPayer($userA, 30.0);
        $exp1->splitEqually([$userA, $userB], 30.0);

        // B pays 30 for C
        $exp2 = TravelExpenseMother::withPayer($userB, 30.0);
        $exp2->splitEqually([$userB, $userC], 30.0);

        // C pays 30 for A
        $exp3 = TravelExpenseMother::withPayer($userC, 30.0);
        $exp3->splitEqually([$userC, $userA], 30.0);

        $result = $this->calculator->calculate([$exp1, $exp2, $exp3], []);
        $balances = $this->indexBalances($result['balances']);

        foreach ($balances as $net) {
            $this->assertEqualsWithDelta(0.0, $net, 0.01);
        }
        $this->assertSame([], $result['transfers']);
    }

    public function testExactBalanceTwoDebtorsOneCreditor(): void
    {
        $userA = UserMother::random();
        $userB = UserMother::random();
        $userC = UserMother::random();
        $idA = (string) $userA->getId()->id();

        $expense = TravelExpenseMother::withPayer($userA, 50.0);
        $expense->splitEqually([$userA, $userB, $userC], 50.0);

        $result = $this->calculator->calculate([$expense], []);
        $balances = $this->indexBalances($result['balances']);

        $this->assertEqualsWithDelta(50.0 - 50.0 / 3, $balances[$idA], 0.01);
        $this->assertCount(2, $result['transfers']);
    }

    public function testDifferenceBelowEpsilonIsIgnored(): void
    {
        $userA = UserMother::random();
        $userB = UserMother::random();

        $travel = TravelMother::random();
        $expense = new TravelExpense($travel, 'tiny', 0.005, TravelExpense::CATEGORY_OTHER, 'EUR', null, null, $userA, TravelExpense::SPLIT_EQUAL, 0.005);
        $expense->splitEqually([$userA, $userB], 0.005);

        $result = $this->calculator->calculate([$expense], []);

        $this->assertSame([], $result['transfers']);
    }

    public function testSettlementReducesBalance(): void
    {
        $userA = UserMother::random();
        $userB = UserMother::random();
        $idA = (string) $userA->getId()->id();
        $idB = (string) $userB->getId()->id();
        $travel = TravelMother::random();

        $expense = TravelExpenseMother::withPayer($userA, 100.0, $travel);
        $expense->splitEqually([$userA, $userB], 100.0);

        $settlement = SettlementMother::between($userB, $userA, 50.0, $travel);

        $result = $this->calculator->calculate([$expense], [$settlement]);
        $balances = $this->indexBalances($result['balances']);

        $this->assertEqualsWithDelta(0.0, $balances[$idA], 0.01);
        $this->assertEqualsWithDelta(0.0, $balances[$idB], 0.01);
        $this->assertSame([], $result['transfers']);
    }

    public function testSettledShareIsExcludedFromCalculation(): void
    {
        $userA = UserMother::random();
        $userB = UserMother::random();
        $idA = (string) $userA->getId()->id();

        $expense = TravelExpenseMother::withPayer($userA, 100.0);
        $expense->splitEqually([$userA, $userB], 100.0);

        foreach ($expense->getShares() as $share) {
            if ($share->getDebtor()->getId()->id() === $userB->getId()->id()) {
                $share->markSettled();
            }
        }

        $result = $this->calculator->calculate([$expense], []);
        $balances = $this->indexBalances($result['balances']);

        $this->assertSame(0.0, $balances[$idA] ?? 0.0);
        $this->assertSame([], $result['transfers']);
    }

    public function testMultiCurrencyUsesAmountInTravelCurrency(): void
    {
        $userA = UserMother::random();
        $userB = UserMother::random();
        $idA = (string) $userA->getId()->id();
        $idB = (string) $userB->getId()->id();
        $travel = TravelMother::random();

        // 100 USD, 1 USD = 0.92 EUR (travelCurrency), amountInTravelCurrency = 92
        $expense = new TravelExpense($travel, 'Hotel', 100.0, TravelExpense::CATEGORY_ACCOMMODATION, 'USD', null, null, $userA, TravelExpense::SPLIT_EQUAL, 92.0, 0.92);
        $expense->splitEqually([$userA, $userB], 92.0);

        $result = $this->calculator->calculate([$expense], []);
        $balances = $this->indexBalances($result['balances']);

        $this->assertSame(46.0, $balances[$idA]);
        $this->assertSame(-46.0, $balances[$idB]);
    }

    public function testAllExpensesSettledReturnsNoTransfers(): void
    {
        $userA = UserMother::random();
        $userB = UserMother::random();
        $travel = TravelMother::random();

        $expense = TravelExpenseMother::withPayer($userA, 100.0, $travel);
        $expense->splitEqually([$userA, $userB], 100.0);

        foreach ($expense->getShares() as $share) {
            $share->markSettled();
        }

        $result = $this->calculator->calculate([$expense], []);

        $this->assertSame([], $result['transfers']);
    }

    public function testGreedyMinimizesTransfersForFourPersons(): void
    {
        $users = [UserMother::random(), UserMother::random(), UserMother::random(), UserMother::random()];
        $travel = TravelMother::random();

        // A pays 120 for all (30 each)
        $exp1 = TravelExpenseMother::withPayer($users[0], 120.0, $travel);
        $exp1->splitEqually($users, 120.0);

        // B pays 80 for all (20 each)
        $exp2 = TravelExpenseMother::withPayer($users[1], 80.0, $travel);
        $exp2->splitEqually($users, 80.0);

        $result = $this->calculator->calculate([$exp1, $exp2], []);

        // Net: A=+90, B=+60, C=-50, D=-100 (approx)
        // Greedy should produce at most 3 transfers (N-1 max for N persons)
        $this->assertLessThanOrEqual(3, count($result['transfers']));

        // Total of transfers should equal total owed
        $totalTransferred = array_sum(array_map(fn ($t) => $t->amount, $result['transfers']));
        $totalOwed = array_sum(array_map(fn ($b) => max(0, $b->netBalance), $result['balances']));
        $this->assertEqualsWithDelta($totalOwed, $totalTransferred, 0.02);
    }

    public function testBalancesAreRoundedToTwoDecimals(): void
    {
        $userA = UserMother::random();
        $userB = UserMother::random();
        $userC = UserMother::random();

        $expense = TravelExpenseMother::withPayer($userA, 10.0);
        $expense->splitEqually([$userA, $userB, $userC], 10.0);

        $result = $this->calculator->calculate([$expense], []);

        foreach ($result['balances'] as $balance) {
            $rounded = round($balance->netBalance, 2);
            $this->assertSame($rounded, $balance->netBalance);
        }
    }
}
