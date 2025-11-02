<?php

namespace App\Tests\Domain\Travel\Model;

use App\Domain\Travel\Model\Travel;
use App\Domain\User\Model\User;
use App\Tests\Domain\Travel\ValueObject\GeoLocationStub;
use App\Tests\Domain\User\Model\UserMother;

class TravelMother
{
    public static function create()
    {
        return new Travel();
    }

    public static function withStars(int $stars): Travel
    {
        $travel = self::create();
        $travel->setStars($stars);

        return $travel;
    }

    public static function random(): Travel
    {
        $travel = self::create();
        $travel->setUser(UserMother::random());
        $travel->setGeoLocation(GeoLocationStub::random());
        $travel->setStars(mt_rand());
        $travel->setWatch(mt_rand());
        $travel->setTitle(uniqid('', true));

        return $travel;
    }

    public static function withUser(User $user): Travel
    {
        $travel = self::random();
        $travel->setUser($user);

        return $travel;
    }

    public static function withTitle(string $title): Travel
    {
        $travel = self::random();
        $travel->setTitle($title);

        return $travel;
    }
}
