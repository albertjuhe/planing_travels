<?php


namespace App\Infrastructure\CoreBundle\Repository;

use App\Infrastructure\CoreBundle\Services\ElasticSearchIndex;

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