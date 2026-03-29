<?php

namespace App\Infrastructure\Application\ElasticSearch\Repository;

use App\Infrastructure\Application\ElasticSearch\Services\ElasticSearchIndex;
use Elastica\Query\Terms;
use FOS\ElasticaBundle\Index\IndexManager;

class ElasticSearchRepository
{
    public const DOCUMENT_INDEX = 'travel';
    public const DOCUMENT_TYPE = 'travel';
    public const PRIMARY_KEY = '_id';

    protected $indexManager;
    protected $index;
    protected $elasticSearchIndex;

    /**
     * ElasticSearchTravelRepository constructor.
     *
     * @param IndexManager       $indexManager
     * @param ElasticSearchIndex $elasticSearchIndex
     */
    public function __construct(
        IndexManager $indexManager,
        ElasticSearchIndex $elasticSearchIndex
    ) {
        $this->elasticSearchIndex = $elasticSearchIndex;
        $this->indexManager = $indexManager;
    }

    protected function getIndex(): \FOS\ElasticaBundle\Elastica\Index
    {
        if ($this->index === null) {
            $this->index = $this->elasticSearchIndex->getOne(static::DOCUMENT_INDEX);
        }

        return $this->index;
    }

    public function find(string $id): iterable
    {
        return $this->findBy(self::PRIMARY_KEY, $id);
    }

    public function findBy(string $field, $value): iterable
    {
        $terms = new Terms();
        $terms->setTerms($field, [$value]);

        return $this->getIndex()->search($terms)->getDocuments();
    }
}
