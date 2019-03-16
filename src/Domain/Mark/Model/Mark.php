<?php

namespace App\Domain\Mark\Model;

use App\Domain\Location\Model\Location;
use App\Domain\Travel\ValueObject\GeoLocation;

class Mark
{
    /** @var string */
    private $id;

    /** @var string */
    private $title;

    /** @var \DateTime */
    protected $createdAt;

    /** @var \DateTime */
    protected $updatedAt;

    /** @var GeoLocation */
    private $geoLocation;

    /** @var string */
    private $json;

    /** @var Location */
    private $location;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->location = new \Doctrine\Common\Collections\ArrayCollection();
        $this->updatedAt = new \DateTime();
        $this->createdAt = new \DateTime();
        $this->geoLocation = new GeoLocation(0, 0, 0, 0, 0, 0);
    }

    public static function fromGeolocationAndId(
        GeoLocation $geoLocation,
        string $id
    ) {
        $mark = new self();
        $mark->setId($id);
        $mark->setGeoLocation($geoLocation);

        return $mark;
    }

    public function equals(Mark $mark)
    {
        return $this->id === $mark->getId();
    }

    /**
     * @return GeoLocation
     */
    public function getGeoLocation(): GeoLocation
    {
        return $this->geoLocation;
    }

    /**
     * @param GeoLocation $geoLocation
     */
    public function setGeoLocation(GeoLocation $geoLocation): void
    {
        $this->geoLocation = $geoLocation;
    }

    /**
     * Get id.
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Set id.
     *
     * @return string
     */
    public function setId(string $id)
    {
        return $this->id = $id;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return Mark
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
     * @return Mark
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
     * Set json.
     *
     * @param string $json
     *
     * @return Mark
     */
    public function setJson($json)
    {
        $this->json = $json;

        return $this;
    }

    /**
     * Get json.
     *
     * @return string
     */
    public function getJson()
    {
        return $this->json;
    }

    /**
     * Add location.
     *
     * @param Location $location
     *
     * @return Mark
     */
    public function addLocation(Location $location)
    {
        $this->location[] = $location;

        return $this;
    }

    /**
     * Remove location.
     *
     * @param Location $location
     */
    public function removeLocation(Location $location)
    {
        $this->location->removeElement($location);
    }

    /**
     * Get location.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set title.
     *
     * @param string $title
     *
     * @return Mark
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

    public function __toString()
    {
        return $this->title;
    }

    public function toArray()
    {
        return [
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'geolocation' => $this->getGeoLocation()->toArray(),
        ];
    }
}
