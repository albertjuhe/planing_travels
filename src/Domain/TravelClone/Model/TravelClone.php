<?php

namespace App\Domain\TravelClone\Model;

class TravelClone
{
    /** @var int */
    private $id;

    /** @var string */
    private $originalTravelId;

    /** @var string */
    private $clonedTravelId;

    /** @var int */
    private $clonedById;

    /** @var int */
    private $originalUserId;

    /** @var string */
    private $originalTravelTitle;

    /** @var \DateTime */
    private $clonedAt;

    /** @var \DateTime */
    private $createdAt;

    /** @var \DateTime */
    private $updatedAt;

    public function __construct(
        string $originalTravelId,
        string $clonedTravelId,
        int $clonedById,
        int $originalUserId,
        string $originalTravelTitle
    ) {
        $this->originalTravelId = $originalTravelId;
        $this->clonedTravelId = $clonedTravelId;
        $this->clonedById = $clonedById;
        $this->originalUserId = $originalUserId;
        $this->originalTravelTitle = $originalTravelTitle;
        $this->clonedAt = new \DateTime();
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOriginalTravelId(): string
    {
        return $this->originalTravelId;
    }

    public function getClonedTravelId(): string
    {
        return $this->clonedTravelId;
    }

    public function getClonedById(): int
    {
        return $this->clonedById;
    }

    public function getOriginalUserId(): int
    {
        return $this->originalUserId;
    }

    public function getOriginalTravelTitle(): string
    {
        return $this->originalTravelTitle;
    }

    public function getClonedAt(): \DateTime
    {
        return $this->clonedAt;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }
}
