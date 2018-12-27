<?php


namespace App\Infrastructure\Application\ElasticSearch\Repository;

use App\Infrastructure\Application\ElasticSearch\Services\ElasticSearchIndex;

class ElasticSearchRepository
{
    /** @var ElasticSearchIndex  */
    protected $elasticSearchIndex;

    /**
     * ElasticSearchRepository constructor.
     * @param $elasticSearchIndex
     */
    public function __construct(ElasticSearchIndex $elasticSearchIndex)
    {
        $this->elasticSearchIndex = $elasticSearchIndex;
    }


}