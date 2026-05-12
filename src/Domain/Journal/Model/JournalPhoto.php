<?php

namespace App\Domain\Journal\Model;

use App\Domain\Location\ValueObject\LocationId;
use Ramsey\Uuid\Uuid;

class JournalPhoto
{
    /** @var string */
    private $id;

    /** @var JournalEntry */
    private $entry;

    /** @var string */
    private $filename;

    /** @var string|null */
    private $caption;

    /** @var \DateTime|null */
    private $takenAt;

    /** @var float|null */
    private $geoLat;

    /** @var float|null */
    private $geoLng;

    /** @var string|null */
    private $linkedLocationId;

    /** @var \DateTime */
    private $createdAt;

    public function __construct(JournalEntry $entry, string $filename)
    {
        $this->id = Uuid::uuid4()->toString();
        $this->entry = $entry;
        $this->filename = $filename;
        $this->createdAt = new \DateTime();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getEntry(): JournalEntry
    {
        return $this->entry;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): void
    {
        $this->filename = $filename;
    }

    public function getCaption(): ?string
    {
        return $this->caption;
    }

    public function setCaption(?string $caption): void
    {
        $this->caption = $caption;
    }

    public function getTakenAt(): ?\DateTime
    {
        return $this->takenAt;
    }

    public function setTakenAt(?\DateTime $takenAt): void
    {
        $this->takenAt = $takenAt;
    }

    public function getGeoLat(): ?float
    {
        return $this->geoLat;
    }

    public function setGeoLat(?float $geoLat): void
    {
        $this->geoLat = $geoLat;
    }

    public function getGeoLng(): ?float
    {
        return $this->geoLng;
    }

    public function setGeoLng(?float $geoLng): void
    {
        $this->geoLng = $geoLng;
    }

    public function getLinkedLocationId(): ?string
    {
        return $this->linkedLocationId;
    }

    public function setLinkedLocationId(?string $linkedLocationId): void
    {
        $this->linkedLocationId = $linkedLocationId;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'filename' => $this->filename,
            'caption' => $this->caption,
            'takenAt' => $this->takenAt ? $this->takenAt->format('Y-m-d H:i') : null,
            'geoLat' => $this->geoLat,
            'geoLng' => $this->geoLng,
            'linkedLocationId' => $this->linkedLocationId,
        ];
    }
}
