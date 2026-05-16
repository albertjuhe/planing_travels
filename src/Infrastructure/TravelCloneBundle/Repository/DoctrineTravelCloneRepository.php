<?php

namespace App\Infrastructure\TravelCloneBundle\Repository;

use App\Domain\TravelClone\Model\TravelClone;
use App\Domain\TravelClone\Repository\TravelCloneRepository;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineTravelCloneRepository implements TravelCloneRepository
{
    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function save(TravelClone $travelClone): void
    {
        $this->entityManager->persist($travelClone);
        $this->entityManager->flush();
    }

    public function findByOriginalTravelId(string $originalTravelId): array
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->select('tc')
            ->from(TravelClone::class, 'tc')
            ->where('tc.originalTravelId = :originalTravelId')
            ->setParameter('originalTravelId', $originalTravelId)
            ->getQuery()
            ->getResult();
    }

    public function findByClonedById(int $clonedById): array
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->select('tc')
            ->from(TravelClone::class, 'tc')
            ->where('tc.clonedById = :clonedById')
            ->setParameter('clonedById', $clonedById)
            ->getQuery()
            ->getResult();
    }

    public function findByClonedTravelId(string $clonedTravelId): ?TravelClone
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->select('tc')
            ->from(TravelClone::class, 'tc')
            ->where('tc.clonedTravelId = :clonedTravelId')
            ->setParameter('clonedTravelId', $clonedTravelId)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
