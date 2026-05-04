<?php

namespace App\Domain\Location\Model;

use App\Domain\Common\Model\AggregateRoot;
use App\Domain\Event\DomainEventPublisher;
use App\Domain\Images\Model\Images;
use App\Domain\Location\Events\LocationWasAdded;
use App\Domain\Location\ValueObject\LocationId;
use App\Domain\Mark\Model\Mark;
use App\Domain\Travel\Model\Travel;
use App\Domain\TypeLocation\Model\TypeLocation;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Location extends AggregateRoot
{
    /** @var LocationId */
    private $id;

    /** @var \DateTime */
    protected $createdAt;

    /** @var \DateTime */
    protected $updatedAt;

    /** @var string */
    private $title;

    /** @var string */
    private $url;

    /** @var string */
    private $slug;

    /** @var string */
    private $description;

    /** @var Mark */
    protected $mark;

    private $notas;

    protected $images;

    /** @var Travel */
    protected $travel;

    /** @var TypeLocation */
    protected $typeLocation;

    /** @var int */
    private $stars;

    /** @var \DateTime|null */
    private $visitAt;

    /** @var Collection|LocationVisitDate[] */
    private $visitDates;

    public function __construct()
    {
        $this->id = new LocationId();
        $this->images = new ArrayCollection();
        $this->notas = new ArrayCollection();
        $this->visitDates = new ArrayCollection();
        $this->updatedAt = new \DateTime();
        $this->createdAt = new \DateTime();
        $this->publishEvent();
    }

    private function publishEvent(): void
    {
        DomainEventPublisher::instance()->publish(
            new LocationWasAdded(
                [
                    'id' => $this->getId()->id(),
                    'createdAt' => $this->createdAt,
                    'updatedAt' => $this->updatedAt,
                ]
            )
        );
    }

    public static function fromCompleteAddress(
        string $placeAddress,
        string $IdType,
        string $link,
        string $comment,
        string $latitude,
        string $longitude,
        string $placeId,
        string $address
    ) {
    }

    public static function fromIdAndTitle(
        string $id,
        string $title
    ) {
        $location = new self();
        $location->setTitle($title);
        $location->id = new LocationId($id);

        return $location;
    }

    public static function fromArray(array $data): Location
    {
        $location = new Location();
        $location->setDescription($data['comment']);
        $location->setUrl($data['link']);
        $location->setTitle($data['placeAddress']);

        return $location;
    }

    public function equalTo(Location $location)
    {
        return $this->id->equalsTo($location->getId());
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return Location
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt.
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt.
     *
     * @param \DateTime $updatedAt
     *
     * @return Location
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt.
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set title.
     *
     * @param string $title
     *
     * @return Location
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set slug.
     *
     * @param string $slug
     *
     * @return Location
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug.
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set description.
     *
     * @param string $description
     *
     * @return Location
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set url.
     *
     * @param string $url
     *
     * @return Location
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url.
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Get mark.
     *
     * @return Mark
     */
    public function getMark()
    {
        return $this->mark;
    }

    /**
     * Set mark.
     *
     * @param Mark $mark
     *
     * @return Location
     */
    public function setMark(Mark $mark = null)
    {
        $this->mark = $mark;

        return $this;
    }

    /**
     * Get travel.
     *
     * @return Travel
     */
    public function getTravel(): Travel
    {
        return $this->travel;
    }

    /**
     * Set travel.
     *
     * @param Travel $travel
     *
     * @return Location
     */
    public function setTravel(Travel $travel)
    {
        $this->travel = $travel;

        return $this;
    }

    /**
     * Get typelocation.
     *
     * @return TypeLocation
     */
    public function getTypeLocation(): TypeLocation
    {
        return $this->typeLocation;
    }

    /**
     * Set travel.
     *
     * @param TypeLocation $typeLocation
     *
     * @return Location
     */
    public function setTypeLocation(TypeLocation $typeLocation)
    {
        $this->typeLocation = $typeLocation;

        return $this;
    }

    /**
     * Get starts.
     *
     * @return int
     */
    public function getStars()
    {
        return $this->stars;
    }

    /**
     * Set starts.
     */
    public function setStars($stars)
    {
        $this->stars = $stars;
    }

    public function getVisitAt(): ?\DateTime
    {
        return $this->visitAt;
    }

    public function setVisitAt(?\DateTime $visitAt): self
    {
        $this->visitAt = $visitAt;

        return $this;
    }

    public function __toString(): string
    {
        return $this->title;
    }

    /**
     * Add images.
     *
     * @param Images $images
     *
     * @return Images
     */
    public function addImages(Images $images): Location
    {
        $this->images[] = $images;

        return $this;
    }

    /**
     * Remove images.
     *
     * @param Images $images
     */
    public function removeImages(Images $images)
    {
        $this->images->removeElement($images);
    }

    /**
     * Get Location.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getImages()
    {
        return $this->images;
    }

    public function getNotas()
    {
        return $this->notas ?? new ArrayCollection();
    }

    /**
     * @return Collection|LocationVisitDate[]
     */
    public function getVisitDates(): Collection
    {
        return $this->visitDates ?? new ArrayCollection();
    }

    public function addVisitDate(\DateTime $date): LocationVisitDate
    {
        foreach ($this->getVisitDates() as $vd) {
            if ($vd->getVisitDate()->format('Y-m-d') === $date->format('Y-m-d')) {
                return $vd;
            }
        }
        $visitDate = new LocationVisitDate($this, $date);
        
        // Set initial position based on existing visit dates for this date in the same travel
        $dateStr = $date->format('Y-m-d');
        $maxPosition = -1;
        
        // Get all visit dates for this date across all locations in the travel
        if ($this->travel !== null && $this->travel->getLocation() !== null) {
            foreach ($this->travel->getLocation() as $loc) {
                if ($loc->getVisitDates() !== null) {
                    foreach ($loc->getVisitDates() as $vd) {
                        if ($vd->getVisitDateString() === $dateStr && $vd->getPosition() !== null) {
                            $maxPosition = max($maxPosition, $vd->getPosition());
                        }
                    }
                }
            }
        }
        $visitDate->setPosition($maxPosition + 1);
        
        $this->getVisitDates()->add($visitDate);

        return $visitDate;
    }

    public function removeVisitDate(\DateTime $date): void
    {
        foreach ($this->getVisitDates() as $vd) {
            if ($vd->getVisitDate()->format('Y-m-d') === $date->format('Y-m-d')) {
                $this->getVisitDates()->removeElement($vd);
                break;
            }
        }
    }

    public function clearVisitDates(): void
    {
        $this->getVisitDates()->clear();
    }

    public function getVisitDateStrings(): array
    {
        $dates = [];
        foreach ($this->getVisitDates() as $vd) {
            $dates[] = $vd->getVisitDate()->format('Y-m-d');
        }
        sort($dates);

        return $dates;
    }

    public function hasVisitDateOn(string $dateStr): bool
    {
        foreach ($this->getVisitDates() as $vd) {
            if ($vd->getVisitDate()->format('Y-m-d') === $dateStr) {
                return true;
            }
        }

        return false;
    }

    public function hasAnyVisitDate(): bool
    {
        return !$this->getVisitDates()->isEmpty();
    }

    public function toArray()
    {
        return [
            'id' => $this->getId()->id(),
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
            'title' => $this->title,
            'url' => $this->url,
            'slug' => $this->slug,
            'description' => $this->description,
            'mark' => $this->mark->getId(),
            'travel' => $this->travel->getId()->id(),
            'typeLocation' => $this->typeLocation->getId(),
            'stars' => $this->stars,
        ];
    }
}
