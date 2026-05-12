<?php

namespace App\Tests\Domain\Budget\Model;

use App\Domain\Budget\Model\Settlement;
use App\Domain\Travel\Model\Travel;
use App\Domain\User\Model\User;
use App\Tests\Domain\Travel\Model\TravelMother;
use App\Tests\Domain\User\Model\UserMother;

class SettlementMother
{
    public static function between(User $from, User $to, float $amount, ?Travel $travel = null): Settlement
    {
        return new Settlement(
            $travel ?? TravelMother::random(),
            $from,
            $to,
            $amount,
            'EUR'
        );
    }

    public static function withCurrency(User $from, User $to, float $amount, string $currency, ?Travel $travel = null): Settlement
    {
        return new Settlement(
            $travel ?? TravelMother::random(),
            $from,
            $to,
            $amount,
            $currency
        );
    }

    public static function random(?Travel $travel = null): Settlement
    {
        return new Settlement(
            $travel ?? TravelMother::random(),
            UserMother::random(),
            UserMother::random(),
            mt_rand(10, 200),
            'EUR'
        );
    }
}
