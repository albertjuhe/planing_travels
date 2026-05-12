<?php

namespace App\Domain\Weather\Service;

use App\Domain\Weather\Model\WeatherForecast;

interface WeatherProvider
{
    /**
     * Returns weather forecast/historical data for the given coordinates and date.
     *
     * @throws \RuntimeException if data cannot be fetched
     */
    public function getForecast(float $lat, float $lng, \DateTime $date): WeatherForecast;
}
