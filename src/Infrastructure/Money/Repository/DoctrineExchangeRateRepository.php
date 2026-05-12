<?php

namespace App\Infrastructure\Money\Repository;

use App\Domain\Money\Model\ExchangeRate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DoctrineExchangeRateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExchangeRate::class);
    }

    public function findFresh(string $from, string $to, \DateTime $date, int $ttlHours = 24): ?ExchangeRate
    {
        $dateStr = $date->format('Y-m-d');
        $staleThreshold = (new \DateTime())->modify("-{$ttlHours} hours");

        return $this->createQueryBuilder('r')
            ->where('r.fromCurrency = :from')
            ->andWhere('r.toCurrency = :to')
            ->andWhere('r.validForDate = :date')
            ->andWhere('r.fetchedAt > :threshold')
            ->setParameter('from', strtoupper($from))
            ->setParameter('to', strtoupper($to))
            ->setParameter('date', $dateStr)
            ->setParameter('threshold', $staleThreshold)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function save(ExchangeRate $rate): void
    {
        $this->getEntityManager()->persist($rate);
        $this->getEntityManager()->flush();
    }
}
