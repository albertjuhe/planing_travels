<?php

namespace App\Infrastructure\TravelBundle\DataTransformer;

use App\Domain\Location\Model\Location;

class ElasticSearchTravelDataTransformer
{
    /** @var Location */
    private $data;

    public function write($elasticTravelResult)
    {
        $this->data = $elasticTravelResult;
    }

    public function read(): array
    {
        $travels = [];

        foreach ($this->data as $document) {
            $travel = [];
            foreach ($document->getData() as $key => $data) {
                $travel[$key] = $data;
            }
            $travel['id'] = $document->getParam('_id');
            $travels[] = $travel;
        }

        return $travels;
    }
}
