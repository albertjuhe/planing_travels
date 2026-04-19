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
}
