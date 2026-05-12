<?php

namespace App\UI\Controller\API;

use App\Domain\Weather\Service\WeatherProvider;
use App\Infrastructure\LocationBundle\Repository\DoctrineLocationRepository;
use App\Infrastructure\TravelBundle\Repository\DoctrineTravelRepository;
use App\Infrastructure\Weather\Repository\DoctrineWeatherForecastRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class WeatherAPIController extends AbstractController
{
    private WeatherProvider $weatherProvider;
    private DoctrineWeatherForecastRepository $forecastRepo;
    private DoctrineLocationRepository $locationRepo;
    private DoctrineTravelRepository $travelRepo;
    private Security $security;

    public function __construct(
        WeatherProvider $weatherProvider,
        DoctrineWeatherForecastRepository $forecastRepo,
        DoctrineLocationRepository $locationRepo,
        DoctrineTravelRepository $travelRepo,
        Security $security
    ) {
        $this->weatherProvider = $weatherProvider;
        $this->forecastRepo = $forecastRepo;
        $this->locationRepo = $locationRepo;
        $this->travelRepo = $travelRepo;
        $this->security = $security;
    }

    #[Route('/api/location/{locationId}/weather', name: 'api_location_weather', methods: ['GET'])]
    public function locationWeather(Request $request, string $locationId): JsonResponse
    {
        try {
            $location = $this->locationRepo->findById($locationId);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Location not found'], 404);
        }

        $dateStr = $request->query->get('date', (new \DateTime())->format('Y-m-d'));
        $date = \DateTime::createFromFormat('Y-m-d', $dateStr) ?: new \DateTime();

        $travel = $location->getTravel();
        $lat = $travel->getLatitude();
        $lng = $travel->getLongitude();

        if ($lat == 0.0 && $lng == 0.0) {
            return new JsonResponse(['error' => 'No coordinates available for this location'], 422);
        }

        try {
            $forecast = $this->forecastRepo->findOrFetch($lat, $lng, $date, $this->weatherProvider);

            return new JsonResponse($forecast->toArray());
        } catch (\Throwable $e) {
            return new JsonResponse(['error' => 'Weather data temporarily unavailable'], 503);
        }
    }

    #[Route('/api/travel/{travelId}/weather', name: 'api_travel_weather', methods: ['GET'])]
    public function travelWeather(string $travelId): JsonResponse
    {
        try {
            $travel = $this->travelRepo->ofIdOrFail($travelId);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Travel not found'], 404);
        }

        $lat = $travel->getLatitude();
        $lng = $travel->getLongitude();

        if ($lat == 0.0 && $lng == 0.0) {
            return new JsonResponse(['forecasts' => [], 'message' => 'No coordinates set for this travel']);
        }

        $forecasts = [];
        $dates = $this->collectTravelDates($travel);

        foreach ($dates as $dateStr) {
            $date = \DateTime::createFromFormat('Y-m-d', $dateStr);
            if (!$date) {
                continue;
            }
            try {
                $forecast = $this->forecastRepo->findOrFetch($lat, $lng, $date, $this->weatherProvider);
                $forecasts[$dateStr] = $forecast->toArray();
            } catch (\Throwable $e) {
                $forecasts[$dateStr] = null;
            }
        }

        return new JsonResponse(['forecasts' => $forecasts, 'travelId' => $travelId]);
    }

    private function collectTravelDates(\App\Domain\Travel\Model\Travel $travel): array
    {
        $dates = [];
        if ($travel->getStartAt() && $travel->getEndAt()) {
            $current = clone $travel->getStartAt();
            $end = clone $travel->getEndAt();
            $maxDays = 16;
            $count = 0;
            while ($current <= $end && $count < $maxDays) {
                $dates[] = $current->format('Y-m-d');
                $current->modify('+1 day');
                $count++;
            }
        }

        return array_unique($dates);
    }
}
