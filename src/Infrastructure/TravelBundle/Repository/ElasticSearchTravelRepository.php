<?php

namespace App\Infrastructure\TravelBundle\Repository;

use App\Domain\Travel\Model\Travel;
use App\Domain\Travel\Repository\IndexerRepository;
use App\Infrastructure\TravelBundle\DataTransformer\ElasticSearchDocumentDataTransformer;
use Elastica\Document;
use App\Infrastructure\Application\ElasticSearch\Repository\ElasticSearchRepository;

class ElasticSearchTravelRepository extends ElasticSearchRepository implements IndexerRepository
{
    public const DOCUMENT_INDEX = 'travel';
    public const DOCUMENT_TYPE = 'travel';

    /**
     * Add travel to elascticsearch.
     *
     * @param Travel $travel
     *
     * @return mixed|void
     */
    public function save(Travel $travel): void
    {
        $elasticSearchDocumentDataTransformer = new ElasticSearchDocumentDataTransformer($travel);

        $travelDocument = new Document(
            $travel->getId()->id(),
            $elasticSearchDocumentDataTransformer->read()
        );
        $this->typeDocument->addDocument($travelDocument);
        $this->refresh();
    }

    public function refresh()
    {
        $this->typeDocument->getIndex()->refresh();
    }
}
