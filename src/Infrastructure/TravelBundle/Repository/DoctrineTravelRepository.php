<?php
namespace App\Infrastructure\TravelBundle\Repository;

use App\Domain\Travel\Exceptions\TravelDoesntExists;
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
     * @param int $maximResults
     * @return mixed|void
     */
    public function TravelsAllOrderedBy($maximResults = 10)
    {
        $q = $this->createQueryBuilder('t')
            ->leftJoin('t.user','user')
            ->addOrderBy('t.starts')
            ->setMaxResults($maximResults)
            ->getQuery();
            return $q->getResult();
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

    /**
     * Find travel by Id
     * @param int $id
     * @return Travel
     */
    public function getTravelById(int $id): Travel
    {
         return $this->findById($id);
    }

    /**
     * @param Travel $travel
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(Travel $travel) {
        $this->_em->persist($travel);
    }

}