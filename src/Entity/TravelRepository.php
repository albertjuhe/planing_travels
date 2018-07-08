<?php

// src/AppBundle/Entity/TravelRepository.php

namespace App\Entity;

use Doctrine\ORM\EntityRepository;

class TravelRepository extends EntityRepository {

    public function findAllOrderedByStarts() {
        return $this->getEntityManager()->createQuery(
            'SELECT t FROM AppBundle:Travel t ORDER BY t.starts DESC'
        )->setMaxResults(10)
        ->getResult();
    }
}