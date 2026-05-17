<?php

namespace App\Infrastructure\WeatherBundle\Provider;

use App\Domain\Travel\ValueObject\GeoLocation;
use App\Domain\Weather\Exceptions\WeatherUnavailable;
use App\Domain\Weather\Model\DailyForecast;
use App\Domain\Weather\Repository\WeatherForecastProvider;
use Psr\Cache\CacheItemPoolInterface;

/**
 * Caches forecasts per coordinate set for a configurable TTL to reduce
 * upstream calls and stay well within OpenWeatherMap free-tier rate limits.
 */
class CachedWeatherProvider implements WeatherForecastProvider
{
    public function __construct(
        private WeatherForecastProvider $inner,
        private CacheItemPoolInterface $cache,
        private int $ttlSeconds = 3600
    ) {
    }

    public function getDailyForecast(GeoLocation $location): array
    {
        $cacheKey = $this->buildCacheKey($location);

        $item = $this->cache->getItem($cacheKey);
        if ($item->isHit()) {
            $cached = $item->get();
            if (is_array($cached)) {
                return $this->hydrate($cached);
            }
        }

        $forecast = $this->inner->getDailyForecast($location);

        $serialised = [];
        foreach ($forecast as $date => $df) {
            $serialised[$date] = [
                'date' => $df->date(),
                'min' => $df->temperatureMin(),
                'max' => $df->temperatureMax(),
                'description' => $df->description(),
                'icon' => $df->icon(),
                'pop' => $df->precipitationProbability(),
                'wind' => $df->windSpeed(),
                'humidity' => $df->humidity(),
            ];
        }

        $item->set($serialised);
        $item->expiresAfter($this->ttlSeconds);
        $this->cache->save($item);

        return $forecast;
    }

    /**
     * @param array<string, array<string, mixed>> $cached
     *
     * @return array<string, DailyForecast>
     */
    private function hydrate(array $cached): array
    {
        $out = [];
        foreach ($cached as $date => $row) {
            if (!is_array($row) || !isset($row['date'], $row['min'], $row['max'])) {
                throw new WeatherUnavailable('Cached weather payload is malformed.');
            }
            $out[$date] = new DailyForecast(
                (string) $row['date'],
                (float) $row['min'],
                (float) $row['max'],
                (string) ($row['description'] ?? ''),
                (string) ($row['icon'] ?? '01d'),
                isset($row['pop']) ? (float) $row['pop'] : null,
                isset($row['wind']) ? (float) $row['wind'] : null,
                isset($row['humidity']) ? (int) $row['humidity'] : null
            );
        }

        return $out;
    }

    private function buildCacheKey(GeoLocation $location): string
    {
        return sprintf('weather_owm_%.4f_%.4f', $location->lat(), $location->lng());
    }
}
