<?php
/**
 * Created by PhpStorm.
 * User: albert.juhe
 * Date: 25/07/2018
 * Time: 08:43
 */

namespace App\Domain\Travel\Model;

class Travel
{
    protected $id;

    protected $title;

    protected $description;

    protected $createdAt;

    protected $updatedAt;

    private $slug;

    private $photo;

    private $lat;

    private $lng;

    private $lat0;

    private $lng0;

    private $lat1;

    private $lng1;

    protected $startAt;

    protected $endAt;

    private $starts;

    private $watch;

    private $gpx;

    private $user;

    private $sharedusers;

    private $location;

    private $publishedAt;

    private $status;



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
    public function getCreatedAt()
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
    public function getUpdatedAt()
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
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * @param mixed $lat
     */
    public function setLat($lat)
    {
        $this->lat = $lat;
    }

    /**
     * @return mixed
     */
    public function getLng()
    {
        return $this->lng;
    }

    /**
     * @param mixed $lng
     */
    public function setLng($lng)
    {
        $this->lng = $lng;
    }

    /**
     * @return mixed
     */
    public function getLat0()
    {
        return $this->lat0;
    }

    /**
     * @param mixed $lat0
     */
    public function setLat0($lat0)
    {
        $this->lat0 = $lat0;
    }

    /**
     * @return mixed
     */
    public function getLng0()
    {
        return $this->lng0;
    }

    /**
     * @param mixed $lng0
     */
    public function setLng0($lng0)
    {
        $this->lng0 = $lng0;
    }

    /**
     * @return mixed
     */
    public function getLat1()
    {
        return $this->lat1;
    }

    /**
     * @param mixed $lat1
     */
    public function setLat1($lat1)
    {
        $this->lat1 = $lat1;
    }

    /**
     * @return mixed
     */
    public function getLng1()
    {
        return $this->lng1;
    }

    /**
     * @param mixed $lng1
     */
    public function setLng1($lng1)
    {
        $this->lng1 = $lng1;
    }

    /**
     * @return mixed
     */
    public function getStartAt()
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
    public function getEndAt()
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
    public function getStarts()
    {
        return $this->starts;
    }

    /**
     * @param mixed $starts
     */
    public function setStarts($starts)
    {
        $this->starts = $starts;
    }

    /**
     * @return mixed
     */
    public function getWatch()
    {
        return $this->watch;
    }

    /**
     * @param mixed $watch
     */
    public function setWatch($watch)
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


}