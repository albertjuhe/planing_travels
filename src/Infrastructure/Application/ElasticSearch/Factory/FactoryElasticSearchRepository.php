<?php
/**
 * Created by PhpStorm.
 * User: ajuhe
 * Date: 22/12/18
 * Time: 18:29
 */

namespace App\Infrastructure\Application\ElasticSearch\Factory;

use App\Infrastructure\Application\ElasticSearch\Repository\ElasticSearchRepository;
use App\Infrastructure\TravelBundle\Repository\ElasticSearchTravelRepository;

/**
 * ElasticSearch manager
 * Class FactoryElasticSearchRepository
 * @package App\Infrastructure\Application\ElasticSearch\Factory
 *
 */
class FactoryElasticSearchRepository
{
    private $elasticSearchTravelRepository;

    public function __construct(ElasticSearchTravelRepository $elasticSearchTravelRepository)
    {
        $this->elasticSearchTravelRepository = $elasticSearchTravelRepository;
    }

    /**
     * Returns elasticsearch repository
     * @param String $entity
     * @return ElasticSearchRepository
     */
    public function Build(String $entity):? ElasticSearchRepository
    {
        switch ($entity) {
            case 'Travel':
                return $this->elasticSearchTravelRepository;
            default:
                return null;
        }
    }
}