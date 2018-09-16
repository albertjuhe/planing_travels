<?php
namespace App\Infrastructure\TravelBundle\Repository;

use App\Domain\Travel\Model\Travel;
use App\Domain\User\Model\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Domain\Travel\Repository\TravelRepository;

class DoctrineTravelRepository extends ServiceEntityRepository implements TravelRepository
{
    /**
     * DoctrineTravelRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Travel::class);
    }

    /**
     * @param int $maximResults
     * @return mixed|void
     */
    public function TravelsAllOrderedByStarts($maximResults = 10) {
        $this->createQueryBuilder('t')
            ->addOrderBy('t.starts')
            ->setMaxResults($maximResults)
            ->getResult();
    }

    /**
     * @param User $user
     * @return mixed
     */
    public function getAllTravelsByUser(User $user)
    {
        $q = $this->createQueryBuilder('t')
            ->leftJoin('t.user','user')
            ->where('user = :user')
            ->setParameter('user',$user)->getQuery();
        return $q->getResult();

    }

    public function getTravelById(int $id): Travel
    {
        // TODO: Implement findById() method.
        return $this->findById($id);
    }

    /**
     * @param Travel $travel
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(Travel $travel) {
        $this->_em->persist($travel);
        $this->_em->flush($travel);
    }

}