<?php

namespace App\Domain\Itinerary\Model;

final class ItineraryLocationInput
{
    public function __construct(
        public readonly string $id,
        public readonly string $title,
        public readonly ?string $description,
        public readonly ?string $typeName,
        public readonly ?float $lat,
        public readonly ?float $lng,
        public readonly bool $isLodging = false,
    ) {
    }
}
