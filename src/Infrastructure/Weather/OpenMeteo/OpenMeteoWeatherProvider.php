<?php

namespace App\Infrastructure\Weather\OpenMeteo;

use App\Domain\Weather\Model\WeatherForecast;
use App\Domain\Weather\Service\WeatherProvider;
use GuzzleHttp\ClientInterface;
use Psr\Log\LoggerInterface;

class OpenMeteoWeatherProvider implements WeatherProvider
{
    private ClientInterface $client;
    private LoggerInterface $logger;
    private string $baseUrl;

    public function __construct(
        ClientInterface $client,
        LoggerInterface $logger,
        string $baseUrl = 'https://api.open-meteo.com/v1'
    ) {
        $this->client = $client;
        $this->logger = $logger;
        $this->baseUrl = rtrim($baseUrl, '/');
    }

    public function getForecast(float $lat, float $lng, \DateTime $date): WeatherForecast
    {
        $dateStr = $date->format('Y-m-d');
        $today = new \DateTime('today');
        $isHistorical = $date < $today;

        $params = [
            'latitude' => round($lat, 4),
            'longitude' => round($lng, 4),
            'daily' => 'temperature_2m_min,temperature_2m_max,weathercode',
            'timezone' => 'auto',
            'start_date' => $dateStr,
            'end_date' => $dateStr,
        ];

        $endpoint = $isHistorical ? '/archive' : '/forecast';
        $url = $this->baseUrl . $endpoint . '?' . http_build_query($params);

        try {
            $response = $this->client->request('GET', $url, ['timeout' => 8]);
            $data = json_decode((string) $response->getBody(), true);

            $daily = $data['daily'] ?? [];
            $tempMin = isset($daily['temperature_2m_min'][0]) ? (float) $daily['temperature_2m_min'][0] : null;
            $tempMax = isset($daily['temperature_2m_max'][0]) ? (float) $daily['temperature_2m_max'][0] : null;
            $code = isset($daily['weathercode'][0]) ? (int) $daily['weathercode'][0] : null;

            $icon = $code !== null ? WeatherForecast::iconFromCode($code) : '❓';
            $description = $code !== null ? WeatherForecast::descriptionFromCode($code) : null;

            return new WeatherForecast(
                $lat, $lng, $date, $tempMin, $tempMax, $code, $icon, $description, $isHistorical
            );
        } catch (\Throwable $e) {
            $this->logger->warning('OpenMeteoWeatherProvider failed', [
                'lat' => $lat, 'lng' => $lng, 'date' => $dateStr, 'error' => $e->getMessage(),
            ]);
            throw new \RuntimeException("Could not fetch weather for {$lat},{$lng} on {$dateStr}: " . $e->getMessage(), 0, $e);
        }
    }
}
