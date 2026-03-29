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
            'id'          => $this->data->getId()->id(),
            'title'       => $this->data->getTitle(),
            'description' => $this->data->getDescription(),
            'createdAt'   => $this->data->getCreatedAt() ? $this->data->getCreatedAt()->format('c') : null,
            'updatedAt'   => $this->data->getUpdatedAt() ? $this->data->getUpdatedAt()->format('c') : null,
            'slug'        => $this->data->getSlug(),
            'latitude'    => $this->data->getGeoLocation()->lat(),
            'longitud'    => $this->data->getGeoLocation()->lng(),
            'startAt'     => $this->data->getStartAt() ? $this->data->getStartAt()->format('c') : null,
            'endAt'       => $this->data->getEndAt() ? $this->data->getEndAt()->format('c') : null,
            'userId'      => $this->data->getUser()->getId()->id(),
            'username'    => $this->data->getUser()->getUsername(),
            'publishedAt' => $this->data->getPublishedAt() ? $this->data->getPublishedAt()->format('c') : null,
            'status'      => $this->data->getStatus(),
            'stars'       => $this->data->getStars(),
            'watch'       => $this->data->getWatch(),
        ];
    }
}
