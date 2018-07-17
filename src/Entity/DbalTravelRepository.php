<?php

// src/AppBundle/Entity/TravelRepository.php

namespace App\Entity;

use App\Domain\Model\Travel\Repository\TravelRepository;
use App\Infrastructure\TravelBundle\Repository\DbalRepository;


class DbalTravelRepository extends DbalRepository implements TravelRepository {

    public function findAllOrderedByStarts($maximResults = 10) {
        return $this->getEntityManager()->createQuery(
            'SELECT t FROM AppBundle:Travel t ORDER BY t.starts DESC'
        )->setMaxResults($maximResults)
        ->getResult();
    }
}