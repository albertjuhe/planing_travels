<?php

namespace App\Tests\Domain\Budget\Model;

use App\Domain\Budget\Model\ExpenseShare;
use App\Domain\Budget\Model\TravelExpense;
use App\Domain\User\Model\User;
use App\Tests\Domain\User\Model\UserMother;

class ExpenseShareMother
{
    public static function forDebtor(User $debtor, float $amount, TravelExpense $expense): ExpenseShare
    {
        return new ExpenseShare($expense, $debtor, $amount, $amount);
    }

    public static function withTravelCurrencyAmount(User $debtor, float $amount, float $amountInTravelCurrency, TravelExpense $expense): ExpenseShare
    {
        return new ExpenseShare($expense, $debtor, $amount, $amountInTravelCurrency);
    }

    public static function settled(User $debtor, float $amount, TravelExpense $expense): ExpenseShare
    {
        $share = new ExpenseShare($expense, $debtor, $amount, $amount);
        $share->markSettled();

        return $share;
    }

    public static function random(TravelExpense $expense): ExpenseShare
    {
        return new ExpenseShare($expense, UserMother::random(), mt_rand(1, 100), mt_rand(1, 100));
    }
}
