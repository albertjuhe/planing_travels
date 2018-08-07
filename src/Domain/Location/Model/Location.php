<?php

namespace App\Domain\Location\Model;

class Location {

    private $id;

    protected $createdAt;

    protected $updatedAt;

    private $title;

    private $url;

    private $slug;

    private $description;

    private $user;

    protected $mark;

    private $notas;

    protected $images;

    protected $travel;

    protected $typeLocation;

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
    public function setTypeLocation($typeLocation = null)
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
    public function addImages($images)
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
