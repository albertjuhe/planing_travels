<?php

namespace App\Entity;

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
use App\Entity\Travel;
use App\Entity\Images;

/**
 *
 * @ORM\Entity
 * @ORM\Table(name="location")
 * @ORM\HasLifecycleCallbacks()
 * @ExclusionPolicy("all")
 *
 */

class Location {
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
     * @var string $url
     * @ORM\Column(name="url", type="string",  length=255, nullable=true)
     * @Expose
     */
    private $url;

    /**
     * @Gedmo\Slug(fields={"title"})
     * @ORM\Column(length=128, unique=true)
     * @Expose
     */
    private $slug;

    /**
     * @var text $descrption
     * @ORM\Column(name="description", type="text", nullable=true)
     * @Expose
     *
     */
    private $description;

    /**
     * @var integer
     * @ORM\ManyToOne(targetEntity="User", inversedBy="location")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="Mark",inversedBy="location")
     * @ORM\JoinColumn(name="mark_id", referencedColumnName="id")
     */
    protected $mark;

    /**
    * @ORM\OneToMany(targetEntity="Nota", mappedBy="location", cascade={"all"})
     * @Expose
     * @SerializedName("notes")
     * @Type("ArrayCollection<App\Entity\Nota>")
     */
    private $notas;

    /**
     * @ORM\OneToMany(targetEntity="Images", mappedBy="location", cascade={"persist"})
     * @Expose
     * @SerializedName("images")
     * @Type("ArrayCollection<App\Entity\Images>")
     */
    protected $images;

    /**
     * @ORM\ManyToOne(targetEntity="Travel", inversedBy="location")
     * @ORM\JoinColumn(name="travel_id", referencedColumnName="id")
     */
    protected $travel;

    /**
     * @ORM\ManyToOne(targetEntity="TypeLocation")
     * @ORM\JoinColumn(name="typeLocation_id", referencedColumnName="id")
     */
    protected $typeLocation;

    /**
     * @var integer
     *
     * @ORM\Column(name="starts", type="integer", nullable=true)
     */
    private $starts;

    public function __construct()
    {
        $this->images = new \Doctrine\Common\Collections\ArrayCollection();
        $this->updatedAt = new \DateTime;
        $this->createdAt = new \DateTime;
    }

    /**
     * Hook on pre-update operations
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
        $this->updatedAt = new \DateTime;
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
     * @return Location
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
     * @return Location
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
     * @return Location
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
     * Set slug
     *
     * @param string $slug
     * @return Location
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Location
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
     * @return Location
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
     * Set url
     *
     * @param string $url
     * @return Location
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Get mark
     *
     * @return \App\Entity\Mark
     */
    public function getMark()
    {
        return $this->mark;
    }

    /**
     * Set mark
     *
     * @param \App\Entity\Mark $mark
     * @return Location
     */
    public function setMark(\App\Entity\Mark $mark = null)
    {
        $this->mark = $mark;

        return $this;
    }



    /**
     * Get travel
     *
     * @return \App\Entity\Travel
     */
    public function getTravel()
    {
        return $this->travel;
    }

    /**
     * Set travel
     *
     * @param \App\Entity\Travel $travel
     * @return Location
     */
    public function setTravel(\App\Entity\Travel $travel = null)
    {
        $this->travel = $travel;

        return $this;
    }

    /**
     * Get typelocation
     *
     * @return \App\Entity\TypeLocation
     */
    public function getTypeLocation()
    {
        return $this->typeLocation;
    }

    /**
     * Set travel
     *
     * @param \App\Entity\TypeLocation $typeLocation
     * @return Location
     */
    public function setTypeLocation(\App\Entity\TypeLocation $typeLocation = null)
    {
        $this->typeLocation = $typeLocation;

        return $this;
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

    public function __toString()
    {
        return $this->title;
    }

    /**
     * Add images
     *
     * @param \App\Entity\Images $images
     * @return Images
     */
    public function addImages(\App\Entity\Images $images)
    {
        $this->images[] = $images;

        return $this;
    }

    /**
     * Remove images
     *
     * @param \App\Entity\Images $images
     */
    public function removeImages(\App\Entity\Images $images)
    {
        $this->images->removeElement($images);
    }

    /**
     * Get Location
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getImages()
    {
        return $this->images;
    }


}
