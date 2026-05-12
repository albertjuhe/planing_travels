<?php

namespace App\Tests\Domain\Budget\Model;

use App\Domain\Budget\Model\TravelExpense;
use App\Domain\Travel\Model\Travel;
use App\Domain\User\Model\User;
use App\Tests\Domain\Travel\Model\TravelMother;
use App\Tests\Domain\User\Model\UserMother;

class TravelExpenseMother
{
    public static function create(Travel $travel, float $amount = 100.0, string $currency = 'EUR'): TravelExpense
    {
        return new TravelExpense(
            $travel,
            'Test expense',
            $amount,
            TravelExpense::CATEGORY_OTHER,
            $currency
        );
    }

    public static function withPayer(User $payer, float $amount = 100.0, ?Travel $travel = null): TravelExpense
    {
        return new TravelExpense(
            $travel ?? TravelMother::random(),
            'Expense with payer',
            $amount,
            TravelExpense::CATEGORY_OTHER,
            'EUR',
            null,
            null,
            $payer,
            TravelExpense::SPLIT_EQUAL,
            $amount,
            1.0
        );
    }

    public static function random(?Travel $travel = null): TravelExpense
    {
        return new TravelExpense(
            $travel ?? TravelMother::random(),
            uniqid('expense_', true),
            mt_rand(10, 500),
            TravelExpense::CATEGORY_OTHER,
            'EUR',
            null,
            null,
            UserMother::random(),
            TravelExpense::SPLIT_EQUAL
        );
    }
}
