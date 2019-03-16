<?php

namespace App\Domain\Travel\DataTransformer;

use App\Domain\Location\Model\Location;

class LocationsTravelArrayDataTransformer implements LocationsTravelDataTransformer
{
    /** @var Location */
    private $data;

    public function write(Location $location)
    {
        $this->data = $location;
    }

    public function read()
    {
        return [
            'id' => $this->data->getId(),
            'title' => $this->data->getTitle(),
            'description' => $this->data->getDescription(),
            'createdAt' => $this->data->getCreatedAt(),
            'slug' => $this->data->getSlug(),
            'userId' => $this->data->getUser()->userId(),
            'mark' => $this->data->getMark()->toArray(),
            'travel' => $this->data->getTravel()->getId(),
            'typeLocation' => $this->data->getTypeLocation()->getId(),
        ];
    }
}
