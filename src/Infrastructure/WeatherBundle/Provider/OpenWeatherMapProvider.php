<?php

namespace App\Infrastructure\WeatherBundle\Provider;

use App\Domain\Travel\ValueObject\GeoLocation;
use App\Domain\Weather\Exceptions\WeatherUnavailable;
use App\Domain\Weather\Model\DailyForecast;
use App\Domain\Weather\Repository\WeatherForecastProvider;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface as HttpClientExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * OpenWeatherMap free tier provider using the 5-day / 3-hour forecast endpoint.
 *
 * @see https://openweathermap.org/forecast5
 */
class OpenWeatherMapProvider implements WeatherForecastProvider
{
    private const ENDPOINT = 'https://api.openweathermap.org/data/2.5/forecast';
    private const TIMEOUT_SECONDS = 5;

    public function __construct(
        private HttpClientInterface $httpClient,
        private string $apiKey,
        private LoggerInterface $logger,
        private string $units = 'metric',
        private string $language = 'en'
    ) {
    }

    public function getDailyForecast(GeoLocation $location): array
    {
        if ($this->apiKey === '') {
            throw new WeatherUnavailable('OpenWeatherMap API key is not configured.');
        }

        try {
            $response = $this->httpClient->request('GET', self::ENDPOINT, [
                'query' => [
                    'lat' => $location->lat(),
                    'lon' => $location->lng(),
                    'appid' => $this->apiKey,
                    'units' => $this->units,
                    'lang' => $this->language,
                ],
                'timeout' => self::TIMEOUT_SECONDS,
            ]);

            $statusCode = $response->getStatusCode();

            if ($statusCode !== 200) {
                $this->logger->warning(sprintf(
                    'OpenWeatherMap returned HTTP %d for lat=%s lon=%s',
                    $statusCode,
                    $location->lat(),
                    $location->lng()
                ));
                throw new WeatherUnavailable('OpenWeatherMap returned HTTP ' . $statusCode);
            }

            $data = $response->toArray(false);
        } catch (HttpClientExceptionInterface $e) {
            throw new WeatherUnavailable('OpenWeatherMap request failed: ' . $e->getMessage(), 0, $e);
        } catch (\JsonException $e) {
            throw new WeatherUnavailable('OpenWeatherMap returned invalid JSON: ' . $e->getMessage(), 0, $e);
        } catch (\Throwable $e) {
            throw new WeatherUnavailable('Unexpected OpenWeatherMap error: ' . $e->getMessage(), 0, $e);
        }

        if (!isset($data['list']) || !is_array($data['list'])) {
            throw new WeatherUnavailable('OpenWeatherMap response is missing the forecast list.');
        }

        return $this->aggregateThreeHourSlotsByDay($data['list'], $data['city']['timezone'] ?? 0);
    }

    /**
     * Aggregates the 3-hour forecast slots into a single DailyForecast per day.
     * The representative slot is the one closest to local 12:00 (midday) to make
     * the icon and description meaningful.
     *
     * @param array<int, array<string, mixed>> $slots
     *
     * @return array<string, DailyForecast>
     */
    private function aggregateThreeHourSlotsByDay(array $slots, int $timezoneOffsetSeconds): array
    {
        $byDay = [];

        foreach ($slots as $slot) {
            if (!isset($slot['dt'])) {
                continue;
            }
            $localTimestamp = (int) $slot['dt'] + $timezoneOffsetSeconds;
            $localDate = gmdate('Y-m-d', $localTimestamp);
            $localHour = (int) gmdate('H', $localTimestamp);

            if (!isset($byDay[$localDate])) {
                $byDay[$localDate] = [
                    'min' => INF,
                    'max' => -INF,
                    'pop' => 0.0,
                    'slots' => [],
                    'representative' => null,
                    'representativeDistance' => PHP_INT_MAX,
                ];
            }

            $tempMin = $slot['main']['temp_min'] ?? $slot['main']['temp'] ?? null;
            $tempMax = $slot['main']['temp_max'] ?? $slot['main']['temp'] ?? null;

            if ($tempMin !== null) {
                $byDay[$localDate]['min'] = min($byDay[$localDate]['min'], (float) $tempMin);
            }
            if ($tempMax !== null) {
                $byDay[$localDate]['max'] = max($byDay[$localDate]['max'], (float) $tempMax);
            }

            $pop = isset($slot['pop']) ? (float) $slot['pop'] : 0.0;
            $byDay[$localDate]['pop'] = max($byDay[$localDate]['pop'], $pop);

            $distanceToMidday = abs($localHour - 12);
            if ($distanceToMidday < $byDay[$localDate]['representativeDistance']) {
                $byDay[$localDate]['representativeDistance'] = $distanceToMidday;
                $byDay[$localDate]['representative'] = $slot;
            }
        }

        $forecasts = [];

        foreach ($byDay as $date => $entry) {
            $rep = $entry['representative'];
            if ($rep === null) {
                continue;
            }

            $weather = $rep['weather'][0] ?? [];
            $description = isset($weather['description']) ? ucfirst((string) $weather['description']) : '';
            $icon = (string) ($weather['icon'] ?? '01d');

            $min = $entry['min'] === INF ? (float) ($rep['main']['temp'] ?? 0) : (float) $entry['min'];
            $max = $entry['max'] === -INF ? (float) ($rep['main']['temp'] ?? 0) : (float) $entry['max'];

            $windSpeed = isset($rep['wind']['speed']) ? (float) $rep['wind']['speed'] : null;
            $humidity = isset($rep['main']['humidity']) ? (int) $rep['main']['humidity'] : null;

            $forecasts[$date] = new DailyForecast(
                $date,
                $min,
                $max,
                $description,
                $icon,
                $entry['pop'],
                $windSpeed,
                $humidity
            );
        }

        return $forecasts;
    }
}
