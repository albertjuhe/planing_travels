<?php

namespace App\Infrastructure\TravelBundle\Repository;

use App\Domain\Travel\Model\Travel;
use App\Domain\Travel\Repository\TravelReadModelRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class DoctrineTravelReadModelRepository extends ServiceEntityRepository implements TravelReadModelRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Travel::class);
    }

    public function getTravelOrderedBy(string $order, int $maximResults): array
    {
        $travels = $this->createQueryBuilder('t')
            ->select('t')
            ->where('t.status = :status')
            ->setParameter('status', Travel::TRAVEL_PUBLISHED)
            ->orderBy('t.createdAt', 'DESC')
            ->setMaxResults($maximResults)
            ->getQuery()
            ->getResult();

        return array_map(fn (Travel $travel) => $travel->toArray(), $travels);
    }

    public function getAllTravelsByUser(int $user): array
    {
        $travels = $this->createQueryBuilder('t')
            ->select('t')
            ->where('t.user = :userId')
            ->setParameter('userId', $user)
            ->orderBy('t.createdAt', 'DESC')
            ->getQuery()
            ->getResult();

        return array_map(fn (Travel $travel) => $travel->toArray(), $travels);
    }
}
