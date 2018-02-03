<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 *
 * @ORM\Entity
 * @ORM\Table(name="mark")
 * @ORM\HasLifecycleCallbacks()
 *
 */

class Mark {
    /**
     * @var string
     *
     * @ORM\Column(name="id", type="string",  length=150)
     * @ORM\Id
     */
    private $id;
    /**
     * @var string $title
     * @ORM\Column(name="title", type="string",  length=255, nullable=false)
     * @Assert\NotBlank(message="You must write a title")

     */
    private $title;
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
     * @var string $lat
     * @ORM\Column(type="decimal", precision=14, scale=8)
     */
    private $lat;

    /**
     * @var string $lng
     * @ORM\Column(type="decimal", precision=14, scale=8)
     */
    private $lng;

    /**
     * @var string $lat0
     * @ORM\Column(type="decimal", precision=14, scale=8, nullable=true)
     */
    private $lat0;

    /**
     * @var string $lng0
     * @ORM\Column(type="decimal", precision=14, scale=8, nullable=true)
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
     */
    private $lng1;

    /**
     * @var text $json
     * @ORM\Column(name="description", type="text", nullable=true)
     *
     */
    private $json;

    /**
     * @ORM\OneToMany(targetEntity="Location", mappedBy="mark")
     */
    private $location;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->location = new \Doctrine\Common\Collections\ArrayCollection();
        $this->updatedAt = new \DateTime;
        $this->createdAt = new \DateTime;
    }

    /**
     * Get id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id
     *
     * @return string
     */
    public function setId($id)
    {
        return $this->id = $id;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Mark
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
     * @return Mark
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
     * Set lat
     *
     * @param string $lat
     * @return Mark
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
     * @return Mark
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
     * Set json
     *
     * @param string $json
     * @return Mark
     */
    public function setJson($json)
    {
        $this->json = $json;

        return $this;
    }

    /**
     * Get json
     *
     * @return string 
     */
    public function getJson()
    {
        return $this->json;
    }

    /**
     * Add location
     *
     * @param \App\Entity\Location $location
     * @return Mark
     */
    public function addLocation(\App\Entity\Location $location)
    {
        $this->location[] = $location;

        return $this;
    }

    /**
     * Remove location
     *
     * @param \App\Entity\Location $location
     */
    public function removeLocation(\App\Entity\Location $location)
    {
        $this->location->removeElement($location);
    }

    /**
     * Get location
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Mark
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
        $this->lng = $lng1;

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
}
