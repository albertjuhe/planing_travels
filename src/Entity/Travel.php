<?php
// src/AppBundle/Entity/Travel.php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\VirtualProperty;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use App\Entity\Location;

/**
 *
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="App\Entity\TravelRepository")
 * @ORM\Table(name="travel")
 * @ORM\HasLifecycleCallbacks()
 *
 * @ExclusionPolicy("all")
 */

class Travel {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Expose
     */
    private $id;

    /**
     * @var DateTime $createdAt
     * @ORM\Column(name="createdAt",type="datetime")
     */
    protected $createdAt;

    /**
     * @var DateTime $updatedAt
     * @ORM\Column(name="updatedAt",type="datetime")
     */
    protected $updatedAt;

    /**
     * @var string $title
     * @ORM\Column(name="title", type="string",  length=255, nullable=false)
     * @Assert\NotBlank(message="You must write a title")
     * @Expose

     */
    private $title;

    /**
     * @Gedmo\Slug(fields={"title"})
     * @ORM\Column(length=128, unique=true)
     * @Expose
     */
    private $slug;

    /**
     * @var string $photo
     * @ORM\Column(name="photo", type="string", length=255, nullable=true)
     */
    private $photo;

    /**
     * @var string $lat
     * @ORM\Column(type="decimal", precision=14, scale=8)
     * @Expose
     */
    private $lat;

    /**
     * @var string $lng
     * @ORM\Column(type="decimal", precision=14, scale=8)
     * @Expose
     */
    private $lng;
    /**
     * @var string $lat0
     * @ORM\Column(type="decimal", precision=14, scale=8, nullable=true)
     * @Expose
     */
    private $lat0;

    /**
     * @var string $lng0
     * @ORM\Column(type="decimal", precision=14, scale=8, nullable=true)
     * @Expose
     */
    private $lng0;

    /**
     * @var string $lat1
     * @ORM\Column(type="decimal", precision=14, scale=8, nullable=true)
     */
    private $lat1;

    /**
     * @var string $lng1
     * @ORM\Column(type="decimal", precision=14, scale=8, nullable=true)
     * @Expose
     */
    private $lng1;

    /**
     * @var DateTime $startAt
     * @ORM\Column(name="startAt",type="datetime")
     * @Expose
     */
    protected $startAt;

    /**
     * @var DateTime $endAt
     * @ORM\Column(name="endAt",type="datetime")
     * @Expose
     */
    protected $endAt;

    /**
     * @var text $descrption
     * @ORM\Column(name="description", type="text", nullable=true)
     * @Expose
     *
     */
    private $description;

    /**
     * @var integer
     * @ORM\ManyToOne(targetEntity="User", inversedBy="travel")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="Location", mappedBy="travel", cascade={"persist"})
     */
    private $location;

    /**
     * @Assert\File(maxSize="6000000")
     */
    private $file;

    /**
     * @var integer
     *
     * @ORM\Column(name="starts", type="integer", nullable=true)
     */
    private $starts;

    /**
     * @var integer
     *
     * @ORM\Column(name="watch", type="integer", nullable=true)
     */
    private $watch;

    /**
     * @ORM\OneToMany(targetEntity="Gpx", mappedBy="travel", cascade={"persist"})
     * @Expose
     * @SerializedName("gpx")
     * @Type("ArrayCollection<App\Entity\Gpx>")
     */
    protected $gpx;

    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="travelsshared", cascade={"persist"})
     */
    protected $sharedusers;

    public function __construct()
    {
        $this->location = new \Doctrine\Common\Collections\ArrayCollection();
        $this->sharedusers = new \Doctrine\Common\Collections\ArrayCollection();
        $this->updatedAt = new \DateTime;
        $this->createdAt = new \DateTime;
        $this->stars = 0;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Travel
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return Travel
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Travel
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set photo
     *
     * @param string $photo
     * @return Travel
     */
    public function setPhoto($photo)
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * Get photo
     *
     * @return string 
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * Set lat
     *
     * @param string $lat
     * @return Travel
     */
    public function setLat($lat)
    {
        $this->lat = $lat;

        return $this;
    }

    /**
     * Get lat
     *
     * @return string 
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * Set lng
     *
     * @param string $lng
     * @return Travel
     */
    public function setLng($lng)
    {
        $this->lng = $lng;

        return $this;
    }

    /**
     * Get lng
     *
     * @return string 
     */
    public function getLng()
    {
        return $this->lng;
    }

    /**
     * Set startAt
     *
     * @param \DateTime $startAt
     * @return Travel
     */
    public function setStartAt($startAt)
    {
        $this->startAt = $startAt;

        return $this;
    }

    /**
     * Get startAt
     *
     * @return \DateTime 
     */
    public function getStartAt()
    {
        return $this->startAt;
    }

    /**
     * Set endAt
     *
     * @param \DateTime $endAt
     * @return Travel
     */
    public function setEndAt($endAt)
    {
        $this->endAt = $endAt;

        return $this;
    }

    /**
     * Get endAt
     *
     * @return \DateTime 
     */
    public function getEndAt()
    {
        return $this->endAt;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Travel
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set user
     *
     * @param \App\Entity\User $user
     * @return Travel
     */
    public function setUser(\App\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \App\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }


    /**
     * Hook on pre-update operations
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
        $this->updatedAt = new \DateTime;
    }

    public function getSlug()
    {
        return $this->slug;
    }


    public function getAbsolutePath()
    {
        return null === $this->photo
            ? null
            : $this->getUploadRootDir().'/'.$this->photo;
    }

    public function getWebPath()
    {
        return null === $this->photo
            ? null
            : $this->getUploadDir().$this->photo;
    }

    public function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'travel/'.$this->getUser()->getUsername().'/'.$this->getId().'/';
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }


    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
        if (isset($this->photo)) {
            // store the old name to delete after the update
            //$this->temp = $this->photo;
            $this->photo = null;
        } else {
            $this->photo = 'initial';
        }
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if (null !== $this->getFile()) {
            // do whatever you want to generate a unique name
            $filename = sha1(uniqid(mt_rand(), true));

            $extensio = $this->getFile()->guessExtension();

            $this->path = $filename.'.'.$extensio;
        }

    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if (null === $this->getFile()) {
            return;
        }

        // if there is an error when moving the file, an exception will
        // be automatically thrown by move(). This will properly prevent
        // the entity from being persisted to the database on error
        $this->getFile()->move($this->getUploadRootDir(), $this->photo);

        // check if we have an old image
        /*
        if (isset($this->temp)) {
            // delete the old image
            unlink($this->getUploadRootDir().'/'.$this->temp);
            // clear the temp image path
            $this->temp = null;
        }
        */
        $this->file = null;
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if ($file = $this->getAbsolutePath()) {
            unlink($file);
        }
    }

    /**
     * Add Location
     *
     * @param \App\Entity\Location $location
     * @return Travel
     */
    public function addLocation(\App\Entity\Location $location)
    {
        $location->setLocation($this);
        $this->location[] = $location;

        return $this;
    }

    /**
     * Remove Location
     *
     * @param \App\Entity\Location $location
     */
    public function removeLocation(\App\Entity\Location $location)
    {
        $this->location->removeElement($location);
    }

    /**
 * Get location
 * @return Collection
 */
    public function getLocation() {
        return $this->location;
    }

    /**
     * Set Location
     *
     * @param Collection $location
     * @return Travel
     */
    public function setLocation($location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get gpx
     * @return Collection
     */
    public function getGpx() {
        return $this->gpx;
    }

    /**
     * Set composiciones
     *
     * @param Collection $gpx
     * @return Travel
     */
    public function setGpx($gpx)
    {
        $this->gpx = $gpx;

        return $this;
    }


    public function __toString() {
        return $this->title;
    }
    /**
     * Set lat
     *
     * @param string $lat0
     * @return Mark
     */
    public function setLat0($lat0)
    {
        $this->lat0 = $lat0;

        return $this;
    }

    /**
     * Get lat0
     *
     * @return string
     */
    public function getLat0()
    {
        return $this->lat0;
    }

    /**
     * Set lng0
     *
     * @param string $lng0
     * @return Mark
     */
    public function setLng0($lng0)
    {
        $this->lng0 = $lng0;

        return $this;
    }

    /**
     * Get lng0
     *
     * @return string
     */
    public function getLng0()
    {
        return $this->lng0;
    }

    /**
     * Set lat1
     *
     * @param string $lat1
     * @return Mark
     */
    public function setLat1($lat1)
    {
        $this->lat1 = $lat1;

        return $this;
    }

    /**
     * Get lat1
     *
     * @return string
     */
    public function getLat1()
    {
        return $this->lat1;
    }

    /**
     * Set lng1
     *
     * @param string $lng
     * @return Mark
     */
    public function setLng1($lng1)
    {
        $this->lng1 = $lng1;

        return $this;
    }

    /**
     * Get lng1
     *
     * @return string
     */
    public function getLng1()
    {
        return $this->lng1;
    }

    /**
     * Get starts
     *
     * @return integer
     */
    public function getStarts()
    {
        return $this->starts;
    }

    /**
     * Set starts
     *
     */
    public function setStarts($starts)
    {
        $this->starts = $starts;
    }

    /**
     * Get watch
     *
     * @return integer
     */
    public function getWatch()
    {
        return $this->watch;
    }

    /**
     * Set watch
     *
     */
    public function setWatch($watch)
    {
        $this->watch = $watch;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Travel
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Add gpx
     *
     * @param \App\Entity\Gpx $gpx
     * @return Travel
     */
    public function addGpx(\App\Entity\Gpx $gpx)
    {
        $this->gpx[] = $gpx;

        return $this;
    }

    /**
     * Remove gpx
     *
     * @param \App\Entity\Gpx $gpx
     */
    public function removeGpx(\App\Entity\Gpx $gpx)
    {
        $this->gpx->removeElement($gpx);
    }

    /**
     * Add sharedusers
     *
     * @param \App\Entity\User $sharedusers
     * @return Travel
     */
    public function addShareduser(\App\Entity\User $sharedusers)
    {
        $this->sharedusers[] = $sharedusers;

        return $this;
    }

    /**
     * Remove sharedusers
     *
     * @param \App\Entity\User $sharedusers
     */
    public function removeShareduser(\App\Entity\User $sharedusers)
    {
        $this->sharedusers->removeElement($sharedusers);
    }

    /**
     * Get sharedusers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSharedusers()
    {
        return $this->sharedusers;
    }

    /**
     * @return int
     */
    public function getStars(): int
    {
        return $this->stars;
    }

    /**
     * @param int $stars
     */
    public function setStars(int $stars)
    {
        $this->stars = $stars;
    }
}
