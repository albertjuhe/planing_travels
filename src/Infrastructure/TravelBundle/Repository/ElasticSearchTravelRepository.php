<?php

namespace App\Infrastructure\TravelBundle\Repository;

use App\Domain\Travel\Model\Travel;
use App\Domain\Travel\Repository\IndexerRepository;
use Elastica\Document;
use App\Infrastructure\Application\ElasticSearch\Repository\ElasticSearchRepository;

class ElasticSearchTravelRepository extends ElasticSearchRepository implements IndexerRepository
{
    const DOCUMENT_INDEX = 'travel';
    const DOCUMENT_TYPE = 'travel';

    /**
     * Add travel to elascticsearch.
     *
     * @param Travel $travel
     *
     * @return mixed|void
     */
    public function save(Travel $travel): void
    {
        $travelDocument = new Document($travel->getId(), $travel->toArray());
        $this->typeDocument->addDocument($travelDocument);
        $this->refresh();
    }

    public function refresh()
    {
        $this->typeDocument->getIndex()->refresh();
    }
}
