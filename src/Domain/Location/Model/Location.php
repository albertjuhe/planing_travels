<?php

namespace App\Domain\Location\Model;

use App\Domain\Images\Model\Images;
use App\Domain\Mark\Model\Mark;
use App\Domain\Travel\Model\Travel;
use App\Domain\TypeLocation\Model\TypeLocation;
use App\Domain\User\Model\User;

class Location
{
    /** @var int */
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

    /** @var \Doctrine\Common\Collections\ArrayCollection */
    protected $images;

    /** @var Travel */
    protected $travel;

    /** @var TypeLocation */
    protected $typeLocation;

    /** @var int */
    private $starts;

    public function __construct()
    {
        $this->images = new \Doctrine\Common\Collections\ArrayCollection();
        $this->updatedAt = new \DateTime();
        $this->createdAt = new \DateTime();
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
        int $id,
        string $title
    ) {
        $location = new self();
        $location->setTitle($title);
        $location->id = $id;

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

    public function equals(Location $location)
    {
        return $this->id === $location->getId();
    }

    /**
     * Get id.
     *
     * @return int
     */
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
    public function getStarts()
    {
        return $this->starts;
    }

    /**
     * Set starts.
     */
    public function setStarts($starts)
    {
        $this->starts = $starts;
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

    public function toArray()
    {
        return [
            'id' => $this->id,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
            'title' => $this->title,
            'url' => $this->url,
            'slug' => $this->slug,
            'description' => $this->description,
            'mark' => $this->mark->getId(),
            'travel' => $this->travel->getId(),
            'typeLocation' => $this->typeLocation->getId(),
            'stars' => $this->starts,
        ];
    }
}
