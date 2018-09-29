<?php
/**
 * Created by PhpStorm.
 * User: albert.juhe
 * Date: 25/07/2018
 * Time: 08:43
 */

namespace App\Domain\Travel\Model;

use App\Domain\User\Model\User;
use App\Domain\Travel\ValueObject\GeoLocation;

class Travel
{
    protected $id;

    protected $title;

    protected $description;

    /** @var \DateTime */
    protected $createdAt;

    /** @var \DateTime */
    protected $updatedAt;

    private $slug;

    private $photo;

    /** @var GeoLocation */
    private $geoLocation;

    /** @var \DateTime */
    protected $startAt;

    /** @var \DateTime */
    protected $endAt;

    /** @var int */
    private $starts;

    private $watch;

    private $gpx;

    private $user;

    private $sharedusers;

    private $location;

    private $publishedAt;

    private $status;

    /**
     * Travel constructor.
     */
    public function __construct()
    {
        $this->updatedAt = new \DateTime;
        $this->createdAt = new \DateTime;
        $this->setStarts(0);
        $this->setWatch(0);
        $this->geoLocation = new GeoLocation(0,0,0,0,0,0);
    }

    public static function fromUser(User $user): Travel {
        $travel = new self();
        $travel->setUser($user);
        return $travel;
    }

    /**
     * @param GeoLocation $geolocation
     * @return Travel
     */
    public static function fromGeoLocation(GeoLocation $geolocation): Travel {
        $travel = new self();
        $travel->setGeoLocation($geolocation);
        return $travel;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param mixed $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return mixed
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param mixed $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    /**
     * @return mixed
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * @param mixed $photo
     */
    public function setPhoto($photo)
    {
        $this->photo = $photo;
    }

    /**
     * @return mixed
     */
    public function getStartAt():? \DateTime
    {
        return $this->startAt;
    }

    /**
     * @param mixed $startAt
     */
    public function setStartAt($startAt)
    {
        $this->startAt = $startAt;
    }

    /**
     * @return mixed
     */
    public function getEndAt():? \DateTime
    {
        return $this->endAt;
    }

    /**
     * @param mixed $endAt
     */
    public function setEndAt($endAt)
    {
        $this->endAt = $endAt;
    }

    /**
     * @return mixed
     */
    public function getStarts(): ?int
    {
        return $this->starts;
    }

    /**
     * @param mixed $starts
     */
    public function setStarts(int $starts)
    {
        $this->starts = $starts;
    }

    /**
     * @return mixed
     */
    public function getWatch(): ?int
    {
        return $this->watch;
    }

    /**
     * @param mixed $watch
     */
    public function setWatch(int $watch)
    {
        $this->watch = $watch;
    }

    /**
     * @return mixed
     */
    public function getGpx()
    {
        return $this->gpx;
    }

    /**
     * @param mixed $gpx
     */
    public function setGpx($gpx): void
    {
        $this->gpx = $gpx;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user): void
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getSharedusers()
    {
        return $this->sharedusers;
    }

    /**
     * @param mixed $sharedusers
     */
    public function setSharedusers($sharedusers): void
    {
        $this->sharedusers = $sharedusers;
    }

    /**
     * @return mixed
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param mixed $location
     */
    public function setLocation($location): void
    {
        $this->location = $location;
    }

    /**
     * @return mixed
     */
    public function getPublishedAt()
    {
        return $this->publishedAt;
    }

    /**
     * @param mixed $publishedAt
     */
    public function setPublishedAt($publishedAt): void
    {
        $this->publishedAt = $publishedAt;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status): void
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getGeoLocation()
    {
        return $this->geoLocation;
    }

    /**
     * @param mixed $geoLocation
     */
    public function setGeoLocation($geoLocation): void
    {
        $this->geoLocation = $geoLocation;
    }

}