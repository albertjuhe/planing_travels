<?php

namespace App\Tests\Domain\Weather\Model;

use App\Domain\Weather\Model\WeatherForecast;
use PHPUnit\Framework\TestCase;

class WeatherForecastTest extends TestCase
{
    public function testConstructorSetsAllFields(): void
    {
        $date = new \DateTime('2024-06-15');
        $forecast = new WeatherForecast(40.4168, -3.7038, $date, 15.5, 28.3, 1, '⛅', 'Mainly clear', false);

        $this->assertSame(40.42, $forecast->getLat());
        $this->assertSame(-3.7, $forecast->getLng());
        $this->assertSame($date, $forecast->getForecastDate());
        $this->assertSame(15.5, $forecast->getTempMin());
        $this->assertSame(28.3, $forecast->getTempMax());
        $this->assertSame(1, $forecast->getWeatherCode());
        $this->assertSame('⛅', $forecast->getIcon());
        $this->assertSame('Mainly clear', $forecast->getDescription());
        $this->assertFalse($forecast->isHistorical());
        $this->assertInstanceOf(\DateTime::class, $forecast->getFetchedAt());
    }

    public function testLatLngRoundedToTwoDecimals(): void
    {
        $forecast = new WeatherForecast(48.85341, 2.34880, new \DateTime(), null, null, null, null, null);

        $this->assertSame(48.85, $forecast->getLat());
        $this->assertSame(2.35, $forecast->getLng());
    }

    public function testHistoricalForecastIsNeverStale(): void
    {
        $forecast = new WeatherForecast(0.0, 0.0, new \DateTime('2020-01-01'), null, null, null, null, null, true);

        $this->assertTrue($forecast->isHistorical());
        $this->assertFalse($forecast->isForecastStale(0));
    }

    public function testFutureForecastIsStaleWithZeroTtl(): void
    {
        $forecast = new WeatherForecast(0.0, 0.0, new \DateTime('tomorrow'), null, null, null, null, null, false);

        $this->assertTrue($forecast->isForecastStale(-1));
    }

    public function testFutureForecastIsNotStaleWhenFresh(): void
    {
        $forecast = new WeatherForecast(0.0, 0.0, new \DateTime('tomorrow'), null, null, null, null, null, false);

        $this->assertFalse($forecast->isForecastStale(6));
    }

    public function testIconFromCodeClearSky(): void
    {
        $this->assertSame('☀️', WeatherForecast::iconFromCode(0));
    }

    public function testIconFromCodeRain(): void
    {
        $this->assertSame('🌧️', WeatherForecast::iconFromCode(63));
    }

    public function testIconFromCodeThunderstorm(): void
    {
        $this->assertSame('⛈️', WeatherForecast::iconFromCode(93));
        $this->assertSame('🌩️', WeatherForecast::iconFromCode(99));
    }

    public function testDescriptionFromCode(): void
    {
        $this->assertSame('Clear sky', WeatherForecast::descriptionFromCode(0));
        $this->assertSame('Moderate rain', WeatherForecast::descriptionFromCode(63));
        $this->assertSame('Thunderstorm', WeatherForecast::descriptionFromCode(95));
        $this->assertSame('Unknown', WeatherForecast::descriptionFromCode(999));
    }

    public function testToArrayContainsRequiredKeys(): void
    {
        $forecast = new WeatherForecast(48.85, 2.35, new \DateTime('2024-06-15'), 12.0, 25.0, 0, '☀️', 'Clear sky');

        $array = $forecast->toArray();

        $this->assertArrayHasKey('date', $array);
        $this->assertArrayHasKey('tempMin', $array);
        $this->assertArrayHasKey('tempMax', $array);
        $this->assertArrayHasKey('weatherCode', $array);
        $this->assertArrayHasKey('icon', $array);
        $this->assertArrayHasKey('description', $array);
        $this->assertArrayHasKey('isHistorical', $array);
    }
}
