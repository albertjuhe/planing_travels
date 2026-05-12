<?php

namespace App\Infrastructure\TravelBundle\Repository;

use App\Domain\Travel\Exceptions\TravelDoesntExists;
use App\Domain\Travel\Model\Travel;
use App\Domain\Travel\ValueObject\TravelId;
use App\Domain\User\Model\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Domain\Travel\Repository\TravelRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

class DoctrineTravelRepository extends ServiceEntityRepository implements TravelRepository
{
    /**
     * DoctrineTravelRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Travel::class);
    }

    public function ofSlugOrFail(string $travelSlug): Travel
    {
        $travel = $this->createQueryBuilder('t')
            ->select('t', 's')
            ->leftJoin('t.sharedusers', 's')
            ->where('t.slug = :slug')
            ->setParameter('slug', $travelSlug)
            ->getQuery()
            ->getOneOrNullResult();

        if (null === $travel) {
            throw new TravelDoesntExists();
        }
        return $travel;
    }

    /**
     * Find travel by Id.
     *
     * @param int $id
     *
     * @return Travel
     */
    public function getTravelById(string $id): Travel
    {
        return $this->findById($id);
    }

    /**
     * @param int $travelId
     *
     * @return Travel
     *
     * @throws TravelDoesntExists
     */
    public function ofIdOrFail(string $travelId): Travel
    {
        $travel = $this->find(new TravelId($travelId));
        if (!$travel instanceof Travel) {
            throw new TravelDoesntExists('Travel doesnt exists');
        }

        return $travel;
    }

    public function getAll(): array
    {
        return $this->findAll();
    }

    /**
     * @param Travel $travel
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(Travel $travel): void
    {
        $this->getEntityManager()->persist($travel);
    }

    public function getAllTravelsByUser(int $userId, int $offset = 0, int $limit = 20): array
    {
        // Get IDs first to avoid JOIN pagination issues
        $qb = $this->createQueryBuilder('t')
            ->select('t.id')
            ->where('t.user = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('t.createdAt', 'DESC');

        if ($limit > 0) {
            $qb->setFirstResult($offset)
               ->setMaxResults($limit);
        }

        $ids = $qb->getQuery()->getResult();

        if (empty($ids)) {
            return [];
        }

        $idList = array_column($ids, 'id');

        return $this->createQueryBuilder('t')
            ->select('t', 'l', 'g')
            ->leftJoin('t.location', 'l')
            ->leftJoin('t.gpx', 'g')
            ->where('t.id IN (:ids)')
            ->setParameter('ids', $idList)
            ->orderBy('t.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getSharedTravelsByUser(int $userId, int $offset = 0, int $limit = 20): array
    {
        // Get IDs first to avoid JOIN pagination issues
        $qb = $this->createQueryBuilder('t')
            ->select('t.id')
            ->innerJoin('t.sharedusers', 's')
            ->where('s.id = :userId')
            ->andWhere('t.user != :userId')
            ->setParameter('userId', $userId)
            ->orderBy('t.createdAt', 'DESC');

        if ($limit > 0) {
            $qb->setFirstResult($offset)
               ->setMaxResults($limit);
        }

        $ids = $qb->getQuery()->getResult();

        if (empty($ids)) {
            return [];
        }

        $idList = array_column($ids, 'id');

        return $this->createQueryBuilder('t')
            ->select('t', 'l', 's', 'g')
            ->leftJoin('t.location', 'l')
            ->leftJoin('t.sharedusers', 's')
            ->leftJoin('t.gpx', 'g')
            ->where('t.id IN (:ids)')
            ->setParameter('ids', $idList)
            ->orderBy('t.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Returns all travels that were cloned from the given source travel ID.
     *
     * @return Travel[]
     */
    public function findClonesOf(string $sourceTravelId): array
    {
        return $this->createQueryBuilder('t')
            ->where('t.clonedFromTravelId = :sourceId')
            ->setParameter('sourceId', $sourceTravelId)
            ->orderBy('t.clonedAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Returns travels cloned by a specific user.
     *
     * @return Travel[]
     */
    public function findClonedByUser(int $userId): array
    {
        return $this->createQueryBuilder('t')
            ->where('t.user = :userId')
            ->andWhere('t.clonedFromTravelId IS NOT NULL')
            ->setParameter('userId', $userId)
            ->orderBy('t.clonedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Returns travels owned by user that have been cloned by others.
     *
     * @return Travel[]
     */
    public function findTravelsClonedFromUser(int $userId): array
    {
        return $this->createQueryBuilder('t')
            ->where('t.user = :userId')
            ->andWhere('t.cloneCount > 0')
            ->setParameter('userId', $userId)
            ->orderBy('t.cloneCount', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
