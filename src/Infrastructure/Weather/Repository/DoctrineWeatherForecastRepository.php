<?php

namespace App\Infrastructure\Weather\Repository;

use App\Domain\Weather\Model\WeatherForecast;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DoctrineWeatherForecastRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WeatherForecast::class);
    }

    public function findCached(float $lat, float $lng, \DateTime $date): ?WeatherForecast
    {
        $latRounded = round($lat, 2);
        $lngRounded = round($lng, 2);
        $dateStr = $date->format('Y-m-d');

        $forecast = $this->createQueryBuilder('w')
            ->where('w.lat = :lat')
            ->andWhere('w.lng = :lng')
            ->andWhere('w.forecastDate = :date')
            ->setParameter('lat', $latRounded)
            ->setParameter('lng', $lngRounded)
            ->setParameter('date', $dateStr)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if ($forecast === null) {
            return null;
        }

        if ($forecast->isForecastStale(6)) {
            return null;
        }

        return $forecast;
    }

    public function save(WeatherForecast $forecast): void
    {
        $this->getEntityManager()->persist($forecast);
        $this->getEntityManager()->flush();
    }

    public function findOrFetch(
        float $lat,
        float $lng,
        \DateTime $date,
        \App\Domain\Weather\Service\WeatherProvider $provider
    ): WeatherForecast {
        $cached = $this->findCached($lat, $lng, $date);
        if ($cached !== null) {
            return $cached;
        }

        $forecast = $provider->getForecast($lat, $lng, $date);
        $this->save($forecast);

        return $forecast;
    }
}
