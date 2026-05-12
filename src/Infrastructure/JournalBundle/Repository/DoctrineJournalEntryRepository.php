<?php

namespace App\Infrastructure\JournalBundle\Repository;

use App\Domain\Journal\Model\JournalEntry;
use App\Domain\Journal\Repository\JournalEntryRepository;
use App\Domain\Travel\Model\Travel;
use App\Domain\User\Model\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DoctrineJournalEntryRepository extends ServiceEntityRepository implements JournalEntryRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JournalEntry::class);
    }

    public function save(JournalEntry $entry): void
    {
        $this->getEntityManager()->persist($entry);
        $this->getEntityManager()->flush();
    }

    public function remove(JournalEntry $entry): void
    {
        $this->getEntityManager()->remove($entry);
        $this->getEntityManager()->flush();
    }

    public function findById(string $id): ?JournalEntry
    {
        return $this->find($id);
    }

    public function findByTravel(Travel $travel, bool $publicOnly = false): array
    {
        $qb = $this->createQueryBuilder('j')
            ->where('j.travel = :travel')
            ->setParameter('travel', $travel)
            ->orderBy('j.entryDate', 'ASC')
            ->addOrderBy('j.createdAt', 'ASC');

        if ($publicOnly) {
            $qb->andWhere('j.isPublic = :public')->setParameter('public', true);
        }

        return $qb->getQuery()->getResult();
    }

    public function findByTravelGroupedByDate(Travel $travel, bool $publicOnly = false): array
    {
        $entries = $this->findByTravel($travel, $publicOnly);
        $grouped = [];

        foreach ($entries as $entry) {
            $dateStr = $entry->getEntryDate()->format('Y-m-d');
            if (!isset($grouped[$dateStr])) {
                $grouped[$dateStr] = [];
            }
            $grouped[$dateStr][] = $entry;
        }

        return $grouped;
    }
}
