<?php

namespace App\Infrastructure\TravelBundle\Repository;

use App\Domain\Travel\Repository\TravelReadModelRepository;
use App\Infrastructure\Application\ElasticSearch\Repository\ElasticSearchRepository;
use App\Infrastructure\TravelBundle\DataTransformer\ElasticSearchTravelDataTransformer;
use App\Domain\Travel\Model\Travel;
use Elastica\Query;
use Elastica\Query\BoolQuery;
use Elastica\Query\Term;

class ElasticSearchReadModelRepository extends ElasticSearchRepository implements TravelReadModelRepository
{
    public const DOCUMENT_INDEX = 'travel';
    public const DOCUMENT_TYPE = 'travel';

    public function getTravelOrderedBy(string $order, int $maxResults): iterable
    {
        $statusFilter = new Term();
        $statusFilter->setTerm('status', Travel::TRAVEL_PUBLISHED);

        $boolQuery = new BoolQuery();
        $boolQuery->addFilter($statusFilter);

        $query = new Query($boolQuery);
        $query->addSort(
            [
                $order => ['order' => 'desc'],
            ]
        );
        $query->setSize($maxResults);

        $result = $this->getIndex()->search($query);
        $documents = $result->getDocuments();
        $elasticSearchTravelDataTransformer = new ElasticSearchTravelDataTransformer();
        $elasticSearchTravelDataTransformer->write($documents);

        return $elasticSearchTravelDataTransformer->read();
    }

    public function getAllTravelsByUser(int $user)
    {
        $documents = $this->findBy('userId', $user);
        $elasticSearchTravelDataTransformer = new ElasticSearchTravelDataTransformer();
        $elasticSearchTravelDataTransformer->write($documents);

        return $elasticSearchTravelDataTransformer->read();
    }
}
