<?php

namespace App\Infrastructure\Middleware;

use App\Infrastructure\TravelBundle\Repository\ElasticSearchTravelRepository;
use League\Tactician\Middleware;

class ElasticSearchMiddleware implements Middleware
{
    private $elasticSearchTravelRepository;

    public function __construct(ElasticSearchTravelRepository $elasticSearchTravelRepository)
    {
        $this->elasticSearchTravelRepository = $elasticSearchTravelRepository;
    }

    public function execute($command, callable $next)
    {
        $returnValue = $next($command);
        //$this->elasticSearchTravelRepository->save($returnValue);
        return $returnValue;
    }
}
