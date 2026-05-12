<?php

namespace App\Domain\Weather\Model;

class WeatherForecast
{
    /** @var int */
    private $id;

    /** @var float */
    private $lat;

    /** @var float */
    private $lng;

    /** @var \DateTime */
    private $forecastDate;

    /** @var float|null */
    private $tempMin;

    /** @var float|null */
    private $tempMax;

    /** @var int|null WMO weather code */
    private $weatherCode;

    /** @var string|null */
    private $icon;

    /** @var string|null */
    private $description;

    /** @var \DateTime */
    private $fetchedAt;

    /** @var bool */
    private $isHistorical;

    public function __construct(
        float $lat,
        float $lng,
        \DateTime $forecastDate,
        ?float $tempMin,
        ?float $tempMax,
        ?int $weatherCode,
        ?string $icon,
        ?string $description,
        bool $isHistorical = false
    ) {
        $this->lat = round($lat, 2);
        $this->lng = round($lng, 2);
        $this->forecastDate = $forecastDate;
        $this->tempMin = $tempMin;
        $this->tempMax = $tempMax;
        $this->weatherCode = $weatherCode;
        $this->icon = $icon;
        $this->description = $description;
        $this->isHistorical = $isHistorical;
        $this->fetchedAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLat(): float
    {
        return $this->lat;
    }

    public function getLng(): float
    {
        return $this->lng;
    }

    public function getForecastDate(): \DateTime
    {
        return $this->forecastDate;
    }

    public function getTempMin(): ?float
    {
        return $this->tempMin;
    }

    public function getTempMax(): ?float
    {
        return $this->tempMax;
    }

    public function getWeatherCode(): ?int
    {
        return $this->weatherCode;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getFetchedAt(): \DateTime
    {
        return $this->fetchedAt;
    }

    public function isHistorical(): bool
    {
        return $this->isHistorical;
    }

    public function isForecastStale(int $ttlHours = 6): bool
    {
        if ($this->isHistorical) {
            return false;
        }
        $diff = (new \DateTime())->getTimestamp() - $this->fetchedAt->getTimestamp();

        return $diff > $ttlHours * 3600;
    }

    public function toArray(): array
    {
        return [
            'date' => $this->forecastDate->format('Y-m-d'),
            'tempMin' => $this->tempMin,
            'tempMax' => $this->tempMax,
            'weatherCode' => $this->weatherCode,
            'icon' => $this->icon,
            'description' => $this->description,
            'isHistorical' => $this->isHistorical,
        ];
    }

    public static function iconFromCode(int $code): string
    {
        if ($code === 0) {
            return '☀️';
        }
        if ($code <= 2) {
            return '⛅';
        }
        if ($code === 3) {
            return '☁️';
        }
        if ($code <= 49) {
            return '🌫️';
        }
        if ($code <= 59) {
            return '🌦️';
        }
        if ($code <= 69) {
            return '🌧️';
        }
        if ($code <= 79) {
            return '🌨️';
        }
        if ($code <= 84) {
            return '🌧️';
        }
        if ($code <= 94) {
            return '⛈️';
        }

        return '🌩️';
    }

    public static function descriptionFromCode(int $code): string
    {
        $map = [
            0 => 'Clear sky', 1 => 'Mainly clear', 2 => 'Partly cloudy', 3 => 'Overcast',
            45 => 'Fog', 48 => 'Icy fog',
            51 => 'Light drizzle', 53 => 'Moderate drizzle', 55 => 'Dense drizzle',
            61 => 'Slight rain', 63 => 'Moderate rain', 65 => 'Heavy rain',
            71 => 'Slight snow', 73 => 'Moderate snow', 75 => 'Heavy snow',
            80 => 'Slight showers', 81 => 'Moderate showers', 82 => 'Violent showers',
            95 => 'Thunderstorm', 96 => 'Thunderstorm with hail', 99 => 'Heavy thunderstorm',
        ];

        return $map[$code] ?? 'Unknown';
    }
}
