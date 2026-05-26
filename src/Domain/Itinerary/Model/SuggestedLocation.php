<?php

namespace App\Domain\Itinerary\Model;

final class SuggestedLocation
{
    public function __construct(
        public readonly string  $name,
        public readonly string  $description,
        public readonly string  $typeName,
        public readonly ?float  $lat,
        public readonly ?float  $lng,
        public readonly ?string $address,
    ) {
    }

    public function toArray(): array
    {
        return [
            'name'        => $this->name,
            'description' => $this->description,
            'typeName'    => $this->typeName,
            'lat'         => $this->lat,
            'lng'         => $this->lng,
            'address'     => $this->address,
        ];
    }
}
