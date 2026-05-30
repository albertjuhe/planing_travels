<?php

namespace App\Application\Command\Location;

use App\Application\Command\Command;

class AddLocationCommand implements Command
{
    private ?string $locationId = null;

    public function __construct(
        private readonly string $travelId,
        private readonly int $userId,
        private readonly string $title,
        private readonly string $address,
        private readonly string $description,
        private readonly string $link,
        private readonly float $latitude,
        private readonly float $longitude,
        private readonly string $placeId,
        private readonly int $locationType,
    ) {
    }

    public function getTravelId(): string
    {
        return $this->travelId;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getLink(): string
    {
        return $this->link;
    }

    public function getLatitude(): float
    {
        return $this->latitude;
    }

    public function getLongitude(): float
    {
        return $this->longitude;
    }

    public function getPlaceId(): string
    {
        return $this->placeId;
    }

    public function getLocationType(): int
    {
        return $this->locationType;
    }

    public function setLocationId(string $locationId): void
    {
        $this->locationId = $locationId;
    }

    public function getLocationId(): ?string
    {
        return $this->locationId;
    }
}
