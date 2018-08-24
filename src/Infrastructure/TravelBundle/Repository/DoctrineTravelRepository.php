<?php
namespace App\Infrastructure\TravelBundle\Repository;

use App\Domain\Travel\Model\Travel;
use App\Domain\User\Model\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Domain\Travel\Repository\TravelRepository;

class DoctrineTravelRepository extends ServiceEntityRepository implements TravelRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Travel::class);
    }

    public function findAllOrderedByStarts($maximResults = 10) {
        $this->createQueryBuilder('t')
            ->addOrderBy('t.starts')
            ->setMaxResults($maximResults)
            ->getResult();
    }

    public function getAllTravelsByUser(User $user)
    {
        $q = $this->createQueryBuilder('t')
            ->leftJoin('t.user','user')
            ->where('user = :user')
            ->setParameter('user',$user)->getQuery();
        return $q->getResult();

    }

    public function save(Travel $travel) {
        $this->_em->persist($travel);
        $this->_em->flush($travel);
    }

}