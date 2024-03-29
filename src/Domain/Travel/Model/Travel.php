<?php

namespace App\Domain\Travel\Model;

use App\Domain\Common\Model\AggregateRoot;
use App\Domain\Gpx\Model\Gpx;
use App\Domain\Location\Model\Location;
use App\Domain\Travel\ValueObject\TravelId;
use App\Domain\User\Model\User;
use App\Domain\Travel\ValueObject\GeoLocation;
use App\Domain\Travel\Events\TravelWasPublished;
use App\Application\DataTransformers\Travel\TravelPublishDataTransformer;
use Doctrine\Common\Collections\ArrayCollection;

class Travel extends AggregateRoot
{
    public const TRAVEL_DRAFT = 10;
    public const TRAVEL_PUBLISHED = 20;

    /** @var TravelId */
    protected $id;

    /** @var string */
    protected $title;

    /** @var string */
    protected $description;

    /** @var \DateTime */
    protected $createdAt;

    /** @var \DateTime */
    protected $updatedAt;

    /** @var string */
    private $slug;

    /** @var string */
    private $photo;

    /** @var GeoLocation */
    private $geoLocation;

    /** @var \DateTime */
    protected $startAt;

    /** @var \DateTime */
    protected $endAt;

    /** @var int */
    private $stars;

    /** @var int */
    private $watch;

    /** @var Gpx */
    private $gpx;

    /** @var User */
    private $user;

    /** @var array */
    private $sharedusers;

    /** @var Location */
    private $location;

    /** @var \DateTime */
    private $publishedAt;

    /** @var int */
    private $status;

    /**
     * Travel constructor.
     */
    public function __construct()
    {
        $this->id = new TravelId();
        $this->updatedAt = new \DateTime();
        $this->createdAt = new \DateTime();
        $this->setStars(0);
        $this->setWatch(0);
        $this->geoLocation = new GeoLocation(0, 0, 0, 0, 0, 0);
        $this->sharedusers = new ArrayCollection();
        $this->status = self::TRAVEL_DRAFT;
    }

    public function equals(Travel $travel)
    {
        return $this->id->equalsTo($travel->id);
    }

    public static function fromUser(User $user): Travel
    {
        $travel = new self();
        $travel->setUser($user);

        return $travel;
    }

    /**
     * @param GeoLocation $geolocation
     *
     * @return Travel
     */
    public static function fromGeoLocation(GeoLocation $geolocation): Travel
    {
        $travel = new self();
        $travel->setGeoLocation($geolocation);

        return $travel;
    }

    public static function fromTitleAndGeolocationAndUser(string $title, GeoLocation $geolocation, User $user)
    {
        $travel = self::fromGeoLocation($geolocation);
        $travel->setTitle($title);
        $travel->setUser($user);

        return $travel;
    }

    /**
     * @return mixed
     */
    public function getId(): TravelId
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId(TravelId $id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getTitle(): ?string
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
    public function getDescription(): ?string
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
    public function setCreatedAt(\DateTime $createdAt)
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
    public function setUpdatedAt(\DateTime $updatedAt)
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
    public function setSlug(string $slug)
    {
        $this->slug = $slug;
    }

    /**
     * @return mixed
     */
    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    /**
     * @param $photo
     */
    public function setPhoto(string $photo)
    {
        $this->photo = $photo;
    }

    /**
     * @return mixed
     */
    public function getStartAt(): ?\DateTime
    {
        return $this->startAt;
    }

    /**
     * @param mixed $startAt
     */
    public function setStartAt(\DateTime $startAt)
    {
        $this->startAt = $startAt;
    }

    /**
     * @return mixed
     */
    public function getEndAt(): ?\DateTime
    {
        return $this->endAt;
    }

    /**
     * @param mixed $endAt
     */
    public function setEndAt(\DateTime $endAt)
    {
        $this->endAt = $endAt;
    }

    /**
     * @return mixed
     */
    public function getStars(): ?int
    {
        return $this->stars;
    }

    /**
     * @param mixed $stars
     */
    public function setStars(int $stars)
    {
        $this->stars = $stars;
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
     * @return Gpx
     */
    public function getGpx()
    {
        return $this->gpx;
    }

    /**
     * @param mixed $gpx
     */
    public function setGpx(Gpx $gpx): void
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
     * Publish the travel, is visible for all.
     */
    public function publish()
    {
        $this->status = self::TRAVEL_PUBLISHED;
        $this->publishedAt = new \DateTime();

        $this->record(
            new TravelWasPublished(
                (new TravelPublishDataTransformer($this))->read(),
                $this->getUser()->getId()->id()
            )
        );

        return $this;
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

    /**
     * info.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'id' => $this->id,
            'title' => $this->getTitle(),
            'description' => $this->getDescription(),
            'createdAt' => $this->getCreatedAt(),
            'updatedAt' => $this->getUpdatedAt(),
            'slug' => $this->getSlug(),
            'latitude' => $this->getGeoLocation()->lat(),
            'longitud' => $this->getGeoLocation()->lng(),
            'startAt' => $this->getStartAt(),
            'endAt' => $this->getEndAt(),
            'userId' => $this->getUser()->getId()->id(),
            'username' => $this->getUser()->getUsername(),
            'publishedAt' => $this->getPublishedAt(),
            'status' => $this->getStatus(),
            'stars' => $this->getStars(),
            'watch' => $this->getWatch(),
        ];
    }

    public function getLatitude(): float
    {
        return $this->getGeoLocation()->lat();
    }

    public function getLongitude(): float
    {
        return $this->getGeoLocation()->lng();
    }
}
