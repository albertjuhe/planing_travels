<?php

// src/AppBundle/Entity/TravelRepository.php

namespace App\Entity;

use App\Domain\Travel\Repository\TravelRepository;
use App\Infrastructure\TravelBundle\Repository\PDOTravelRepository;

/*
 * @Deprecated
 */
class DbalTravelRepository extends PDOTravelRepository implements TravelRepository {

    public function findAllOrderedByStarts($maximResults = 10) {
/*        return $this->getEntityManager()->createQuery(
            'SELECT t FROM AppBundle:Travel t ORDER BY t.starts DESC'
        )->setMaxResults($maximResults)
        ->getResult();*/
    }
}