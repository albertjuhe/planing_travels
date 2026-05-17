<?php

namespace App\Domain\Weather\Model;

class DailyForecast
{
    private string $date;
    private float $temperatureMin;
    private float $temperatureMax;
    private string $description;
    private string $icon;
    private ?float $precipitationProbability;
    private ?float $windSpeed;
    private ?int $humidity;

    public function __construct(
        string $date,
        float $temperatureMin,
        float $temperatureMax,
        string $description,
        string $icon,
        ?float $precipitationProbability = null,
        ?float $windSpeed = null,
        ?int $humidity = null
    ) {
        $this->date = $date;
        $this->temperatureMin = $temperatureMin;
        $this->temperatureMax = $temperatureMax;
        $this->description = $description;
        $this->icon = $icon;
        $this->precipitationProbability = $precipitationProbability;
        $this->windSpeed = $windSpeed;
        $this->humidity = $humidity;
    }

    public function date(): string
    {
        return $this->date;
    }

    public function temperatureMin(): float
    {
        return $this->temperatureMin;
    }

    public function temperatureMax(): float
    {
        return $this->temperatureMax;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function icon(): string
    {
        return $this->icon;
    }

    public function precipitationProbability(): ?float
    {
        return $this->precipitationProbability;
    }

    public function windSpeed(): ?float
    {
        return $this->windSpeed;
    }

    public function humidity(): ?int
    {
        return $this->humidity;
    }

    public function toArray(): array
    {
        return [
            'date' => $this->date,
            'temperatureMin' => round($this->temperatureMin, 1),
            'temperatureMax' => round($this->temperatureMax, 1),
            'description' => $this->description,
            'icon' => $this->icon,
            'iconUrl' => sprintf('https://openweathermap.org/img/wn/%s@2x.png', $this->icon),
            'precipitationProbability' => $this->precipitationProbability !== null
                ? round($this->precipitationProbability * 100)
                : null,
            'windSpeed' => $this->windSpeed !== null ? round($this->windSpeed, 1) : null,
            'humidity' => $this->humidity,
        ];
    }
}
