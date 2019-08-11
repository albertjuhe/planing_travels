<?php

namespace App\Infrastructure\TypeLocationBundle\Repository;

use App\Domain\TypeLocation\Exceptions\TypeLocationDoesntExists;
use App\Domain\TypeLocation\Repository\TypeLocationRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Domain\TypeLocation\Model\TypeLocation;

class DoctrineTypeLocation extends ServiceEntityRepository implements TypeLocationRepository
{
    /**
     * DoctrineTravelRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypeLocation::class);
    }

    /**
     * Get all type of locations.
     *
     * @return array|mixed
     */
    public function getAllTypeLocations()
    {
        return $this->findAll();
    }

    public function idOrFail(string $locationType): TypeLocation
    {
        $locationType = $this->find($locationType);
        if (!$locationType instanceof TypeLocation) {
            throw new TypeLocationDoesntExists();
        }

        return $locationType;
    }
}
