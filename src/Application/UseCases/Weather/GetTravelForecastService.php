<?php

namespace App\Application\UseCases\Weather;

use App\Application\Query\Weather\GetTravelForecastQuery;
use App\Application\UseCases\UsesCasesService;
use App\Domain\Travel\Exceptions\TravelDoesntExists;
use App\Domain\Travel\Model\Travel;
use App\Domain\Travel\Repository\TravelRepository;
use App\Domain\Travel\ValueObject\GeoLocation;
use App\Domain\User\ValueObject\UserId;
use App\Domain\Weather\Exceptions\WeatherUnavailable;
use App\Domain\Weather\Model\DailyForecast;
use App\Domain\Weather\Repository\WeatherForecastProvider;
use Psr\Log\LoggerInterface;

class GetTravelForecastService implements UsesCasesService
{
    /**
     * Free OpenWeatherMap 5-day/3-hour forecast covers roughly 5 days from today.
     */
    private const FORECAST_WINDOW_DAYS = 5;

    public function __construct(
        private TravelRepository $travelRepository,
        private WeatherForecastProvider $weatherProvider,
        private LoggerInterface $logger
    ) {
    }

    /**
     * @return array{
     *     available: bool,
     *     reason?: string,
     *     forecast?: array<string, array<string, mixed>>
     * }
     */
    public function __invoke(GetTravelForecastQuery $query): array
    {
        $travel = $this->travelRepository->ofSlugOrFail($query->getSlug());

        if (null === $travel) {
            throw new TravelDoesntExists();
        }

        if (!$this->userCanSeeForecast($travel, $query->getUserId())) {
            return ['available' => false, 'reason' => 'forbidden'];
        }

        $today = new \DateTimeImmutable('today');
        $windowEnd = $today->modify('+' . (self::FORECAST_WINDOW_DAYS - 1) . ' days');

        $datesByCoords = $this->collectDatesGroupedByCoords($travel, $today, $windowEnd);

        if (empty($datesByCoords)) {
            return ['available' => false, 'reason' => 'out_of_window'];
        }

        $forecastByDate = [];

        foreach ($datesByCoords as $coordsKey => $payload) {
            try {
                $providerForecast = $this->weatherProvider->getDailyForecast($payload['geoLocation']);
            } catch (WeatherUnavailable $e) {
                $this->logger->warning('Weather provider unavailable for travel ' . $query->getSlug() . ': ' . $e->getMessage());
                continue;
            } catch (\Throwable $e) {
                $this->logger->error('Unexpected weather provider error: ' . $e->getMessage());
                continue;
            }

            foreach ($payload['dates'] as $dateStr) {
                if (isset($providerForecast[$dateStr])) {
                    /** @var DailyForecast $df */
                    $df = $providerForecast[$dateStr];
                    $forecastByDate[$dateStr] = $df->toArray();
                }
            }
        }

        if (empty($forecastByDate)) {
            return ['available' => false, 'reason' => 'unavailable'];
        }

        ksort($forecastByDate);

        return [
            'available' => true,
            'forecast' => $forecastByDate,
        ];
    }

    private function userCanSeeForecast(Travel $travel, ?int $userId): bool
    {
        if ($userId === null) {
            return false;
        }

        $currentUserId = new UserId($userId);

        if ($travel->getUser()->getId()->equalsTo($currentUserId)) {
            return true;
        }

        foreach ($travel->getSharedusers() as $sharedUser) {
            if ($sharedUser->getId()->equalsTo($currentUserId)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Walks through travel dates within the forecast window and groups them by
     * the coordinates of the first scheduled location of that day. Falls back to
     * the travel's own geolocation when no location is scheduled on that day.
     *
     * @return array<string, array{geoLocation: GeoLocation, dates: string[]}>
     */
    private function collectDatesGroupedByCoords(
        Travel $travel,
        \DateTimeImmutable $windowStart,
        \DateTimeImmutable $windowEnd
    ): array {
        $startAt = $travel->getStartAt();
        $endAt = $travel->getEndAt();

        if ($startAt === null || $endAt === null) {
            return [];
        }

        $tripStart = \DateTimeImmutable::createFromMutable($startAt)->setTime(0, 0, 0);
        $tripEnd = \DateTimeImmutable::createFromMutable($endAt)->setTime(0, 0, 0);

        $rangeStart = max($tripStart, $windowStart);
        $rangeEnd = min($tripEnd, $windowEnd);

        if ($rangeStart > $rangeEnd) {
            return [];
        }

        $grouped = [];
        $cursor = $rangeStart;

        while ($cursor <= $rangeEnd) {
            $dateStr = $cursor->format('Y-m-d');
            $coords = $this->resolveCoordinatesForDate($travel, $dateStr);

            if ($coords !== null) {
                $key = $this->coordsKey($coords);
                if (!isset($grouped[$key])) {
                    $grouped[$key] = ['geoLocation' => $coords, 'dates' => []];
                }
                $grouped[$key]['dates'][] = $dateStr;
            }

            $cursor = $cursor->modify('+1 day');
        }

        return $grouped;
    }

    private function resolveCoordinatesForDate(Travel $travel, string $dateStr): ?GeoLocation
    {
        $locations = $travel->getLocationsForDate($dateStr);

        foreach ($locations as $location) {
            $mark = $location->getMark();
            if ($mark === null) {
                continue;
            }
            $geo = $mark->getGeoLocation();
            if ($geo === null) {
                continue;
            }
            if ($this->isValidCoordinate($geo)) {
                return $geo;
            }
        }

        $travelGeo = $travel->getGeoLocation();
        if ($travelGeo !== null && $this->isValidCoordinate($travelGeo)) {
            return $travelGeo;
        }

        return null;
    }

    private function isValidCoordinate(GeoLocation $geo): bool
    {
        return !($geo->lat() === 0.0 && $geo->lng() === 0.0);
    }

    private function coordsKey(GeoLocation $geo): string
    {
        return sprintf('%.4f,%.4f', $geo->lat(), $geo->lng());
    }
}
