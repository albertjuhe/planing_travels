<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\VirtualProperty;
use JMS\Serializer\Annotation\Type;
use App\Entity\Location;

/**
 *
 * @ORM\Entity
 * @ORM\Table(name="images")
 * @ORM\HasLifecycleCallbacks()
 * @ExclusionPolicy("all")
 *
 */
class Images
{
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
     * @var string $original
     * @ORM\Column(name="original", type="string",  length=255, nullable=false)
     */
    private $original;

    /**
     * @var string $filename
     * @ORM\Column(name="filename", type="string",  length=255, nullable=false)
     * @Expose
     */
    private $filename;

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
     * @ORM\ManyToOne(targetEntity="Location",inversedBy="images")
     * @ORM\JoinColumn(name="location_id", referencedColumnName="id")
     */
    private $location;


    public function __construct()
    {

        $this->updatedAt = new \DateTime;
        $this->createdAt = new \DateTime;
    }
    /**
     * Pre Persist method.
     */
    public function prePersist()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }
    /**
     * Pre Update method.
     */
    public function preUpdate()
    {
        $this->updatedAt = new \DateTime();
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
     * Set original
     *
     * @param string $original
     * @return Images
     */
    public function setOriginal($original)
    {
        $this->original = $original;

        return $this;
    }

    /**
     * Get original
     *
     * @return string 
     */
    public function getOriginal()
    {
        return $this->original;
    }

    /**
     * Set filename
     *
     * @param string $filename
     * @return Images
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Get filename
     *
     * @return string 
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Images
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
     * @return Images
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
     * Get location
     *
     * @return \App\Entity\Location
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set location
     *
     * @param \App\Entity\Location $location
     * @return Images
     */
    public function setLocation(\App\Entity\Location $location = null)
    {
        $this->location = $location;

        return $this;
    }

    public function __toString() {
        return $this->filename;
    }
}
