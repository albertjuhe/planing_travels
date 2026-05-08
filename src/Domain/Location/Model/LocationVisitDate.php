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

    /** @var \DateTime|null */
    private $timeStart;

    /** @var \DateTime|null */
    private $timeEnd;

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

    public function getTimeStart(): ?\DateTime
    {
        return $this->timeStart;
    }

    public function setTimeStart(?\DateTime $timeStart): void
    {
        $this->timeStart = $timeStart;
    }

    public function getTimeEnd(): ?\DateTime
    {
        return $this->timeEnd;
    }

    public function setTimeEnd(?\DateTime $timeEnd): void
    {
        $this->timeEnd = $timeEnd;
    }

    public function getTimeStartString(): ?string
    {
        return $this->timeStart ? $this->timeStart->format('H:i') : null;
    }

    public function getTimeEndString(): ?string
    {
        return $this->timeEnd ? $this->timeEnd->format('H:i') : null;
    }
}
