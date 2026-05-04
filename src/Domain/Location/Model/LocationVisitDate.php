<?php

namespace App\Domain\Location\Model;

class LocationVisitDate
{
    /** @var int */
    private $id;

    /** @var Location */
    private $location;

    /** @var \DateTime */
    private $visitDate;

    /** @var int|null */
    private $position;

    public function __construct(Location $location, \DateTime $visitDate)
    {
        $this->location = $location;
        $this->visitDate = $visitDate;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLocation(): Location
    {
        return $this->location;
    }

    public function getVisitDate(): \DateTime
    {
        return $this->visitDate;
    }

    public function getVisitDateString(): string
    {
        return $this->visitDate->format('Y-m-d');
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): void
    {
        $this->position = $position;
    }
}
