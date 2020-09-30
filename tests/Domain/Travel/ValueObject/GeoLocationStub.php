<?php

namespace App\Tests\Domain\Travel\ValueObject;

use App\Domain\Travel\ValueObject\GeoLocation;

class GeoLocationStub
{
    public static function create(
        float $latitud = 0,
        float $longitud = 0,
        float $latitud0 = 0,
        float $longitud0 = 0,
        float $latitud1 = 0,
        float $longitud1 = 0
    ) {
        return new GeoLocation(
            $latitud,
            $longitud,
            $latitud0,
            $longitud0,
            $latitud1,
            $longitud1
        );
    }

    public static function withLatitude(float $latitude): GeoLocation
    {
        return self::create($latitude);
    }

    public static function withLongitudAndLatitude(float $longitud, float $latitude): GeoLocation
    {
        return self::create($longitud, $latitude);
    }

    public static function random()
    {
        return new GeoLocation(
            mt_rand(),
            mt_rand(),
            mt_rand(),
            mt_rand(),
            mt_rand(),
            mt_rand()
        );
    }
}
