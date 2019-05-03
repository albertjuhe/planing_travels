<?php

namespace App\Infrastructure\Application\ElasticSearch\Repository;

use App\Infrastructure\Application\ElasticSearch\Services\ElasticSearchIndex;
use Elastica\Query\Terms;
use FOS\ElasticaBundle\Index\IndexManager;

class ElasticSearchRepository
{
    const DOCUMENT_INDEX = 'travel';
    const DOCUMENT_TYPE = 'travel';
    const PRIMARY_KEY = '_id';

    protected $indexManager;
    protected $index;
    protected $typeDocument;
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
        $this->index = $this->elasticSearchIndex->getOne(static::DOCUMENT_INDEX);
        $this->typeDocument = $this->index->getType(static::DOCUMENT_TYPE);
    }

    public function find(string $id): iterable
    {
        return $this->findBy(self::PRIMARY_KEY, $id);
    }

    public function findBy(string $field, $value): iterable
    {
        $terms = new Terms();
        $terms->setTerms($field, [$value]);

        return $this->index->search($terms)->getDocuments();
    }
}
