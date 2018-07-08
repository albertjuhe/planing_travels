<?php

// src/AppBundle/Entity/TravelRepository.php

namespace App\Entity;

use App\Domain\Model\DbalRepository;
use App\Domain\Model\TravelRepository;


class DbalTravelRepository extends DbalRepository implements TravelRepository {

    public function findAllOrderedByStarts($maximResults = 10) {
        return $this->getEntityManager()->createQuery(
            'SELECT t FROM AppBundle:Travel t ORDER BY t.starts DESC'
        )->setMaxResults($maximResults)
        ->getResult();
    }
}