<?php

namespace App\Infrastructure\TravelBundle\DataTransformer;

use App\Domain\Travel\Model\Travel;

class ElasticSearchDocumentDataTransformer
{
    /** @var Travel */
    private $data;

    public function __construct(Travel $travel)
    {
        $this->write($travel);
    }

    public function write(Travel $travel)
    {
        $this->data = $travel;
    }

    public function read(): array
    {
        return [
            'id' => $this->data->getId()->id(),
            'title' => $this->data->getTitle(),
            'description' => $this->data->getDescription(),
            'createdAt' => $this->data->getCreatedAt(),
            'updatedAt' => $this->data->getUpdatedAt(),
            'slug' => $this->data->getSlug(),
            'latitude' => $this->data->getGeoLocation()->lat(),
            'longitud' => $this->data->getGeoLocation()->lng(),
            'startAt' => $this->data->getStartAt(),
            'endAt' => $this->data->getEndAt(),
            'userId' => $this->data->getUser()->getId()->id(),
            'username' => $this->data->getUser()->getUsername(),
            'publishedAt' => $this->data->getPublishedAt(),
            'status' => $this->data->getStatus(),
            'stars' => $this->data->getStars(),
            'watch' => $this->data->getWatch(),
        ];
    }
}
