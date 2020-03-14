<?php

namespace App\Infrastructure\LocationBundle\Repository;

use App\Domain\Location\Exceptions\LocationDoesntExists;
use App\Domain\Location\Model\Location;
use App\Domain\Location\Repository\LocationRepository;
use App\Domain\Location\ValueObject\LocationId;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class DoctrineLocationRepository extends ServiceEntityRepository implements LocationRepository
{
    /**
     * DoctrineTravelRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Location::class);
    }

    public function save(Location $location): void
    {
        $this->_em->persist($location);
    }

    public function remove(Location $location): void
    {
        $this->_em->remove($location);
    }

    public function findById(string $locationId): Location
    {
        $location = $this->find(new LocationId($locationId));
        if (null === $location) {
            throw new LocationDoesntExists();
        }

        return $location;
    }
}
