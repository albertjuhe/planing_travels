<?php


namespace App\Infrastructure\LocationBundle\Repository;


use App\Domain\Location\Model\Location;
use App\Domain\Location\Repository\LocationRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class DoctrineLocationRepository extends ServiceEntityRepository implements LocationRepository
{
    /**
     * DoctrineTravelRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Location::class);
    }

    public function save(Location $location)
    {
        $this->_em->persist($location);
    }

}