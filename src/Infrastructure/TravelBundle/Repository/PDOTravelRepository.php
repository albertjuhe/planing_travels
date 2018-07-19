<?php
namespace App\Infrastructure\TravelBundle\Repository;

use Doctrine\ORM\EntityRepository;
use App\Domain\Travel\Repository\TravelRepository;

class PDOTravelRepository extends EntityRepository implements TravelRepository
{
    public function findAllOrderedByStarts($maximResults = 10) {
        return $this->getEntityManager()->createQuery(
            'SELECT t FROM AppBundle:Travel t ORDER BY t.starts DESC'
        )->setMaxResults($maximResults)
            ->getResult();
    }

}