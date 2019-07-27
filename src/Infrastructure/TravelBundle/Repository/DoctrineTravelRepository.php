<?php

namespace App\Infrastructure\TravelBundle\Repository;

use App\Domain\Travel\Exceptions\TravelDoesntExists;
use App\Domain\Travel\Model\Travel;
use App\Domain\Travel\ValueObject\TravelId;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Domain\Travel\Repository\TravelRepository;

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
        /** @var Travel $travel */
        $travel = $this->findOneBy(['slug' => $travelSlug]);
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
        if (null === $travel) {
            throw new TravelDoesntExists();
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
        $this->_em->persist($travel);
    }
}
