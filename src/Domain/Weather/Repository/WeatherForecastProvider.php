<?php

namespace App\Domain\Weather\Repository;

use App\Domain\Travel\ValueObject\GeoLocation;
use App\Domain\Weather\Model\DailyForecast;

interface WeatherForecastProvider
{
    /**
     * Returns a daily forecast indexed by date (Y-m-d) for the given coordinates.
     * Only dates within the provider's free forecast window will be returned.
     *
     * @return array<string, DailyForecast>
     *
     * @throws \App\Domain\Weather\Exceptions\WeatherUnavailable
     */
    public function getDailyForecast(GeoLocation $location): array;
}
