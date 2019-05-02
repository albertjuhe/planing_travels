<?php

namespace App\Infrastructure\TravelBundle\Repository;

use App\Domain\Travel\Repository\TravelReadModelRepository;
use App\Infrastructure\Application\ElasticSearch\Repository\ElasticSearchRepository;
use App\Infrastructure\TravelBundle\DataTransformer\ElasticSearchTravelDataTransformer;
use Elastica\Query;
use Elastica\Query\Terms;

class ElasticSearchReadModelRepository extends ElasticSearchRepository implements TravelReadModelRepository
{
    const DOCUMENT_INDEX = 'travel';
    const DOCUMENT_TYPE = 'travel';

    public function getTravelOrderedBy(string $order, int $maxResults): iterable
    {
        $query = new Query();
        $query->addSort(
            [
                $order => ['order' => 'asc'],
            ]
        );

        $result = $this->index->search($query, ['limit' => $maxResults]);
        $documents = $result->getDocuments();
        $elasticSearchTravelDataTransformer = new ElasticSearchTravelDataTransformer();
        $elasticSearchTravelDataTransformer->write($documents);

        return $elasticSearchTravelDataTransformer->read();
    }

    public function getAllTravelsByUser(int $user)
    {
        $terms = new Terms();
        $terms->setTerms('userId', [$user]);
        $result = $this->index->search($terms);
        $documents = $result->getDocuments();
        $elasticSearchTravelDataTransformer = new ElasticSearchTravelDataTransformer();
        $elasticSearchTravelDataTransformer->write($documents);

        return $elasticSearchTravelDataTransformer->read();
    }
}
