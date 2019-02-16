<?php

namespace App\Domain\Images\Model;

class Images
{
    private $id;

    private $original;

    private $filename;

    protected $createdAt;

    protected $updatedAt;

    private $location;

    public function __construct()
    {
        $this->updatedAt = new \DateTime();
        $this->createdAt = new \DateTime();
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
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set original.
     *
     * @param string $original
     *
     * @return Images
     */
    public function setOriginal($original)
    {
        $this->original = $original;

        return $this;
    }

    /**
     * Get original.
     *
     * @return string
     */
    public function getOriginal()
    {
        return $this->original;
    }

    /**
     * Set filename.
     *
     * @param string $filename
     *
     * @return Images
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Get filename.
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return Images
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
     * @return Images
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
     * Get location.
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set location.
     *
     * @param \App\Domain\Location\Model\Location $location
     *
     * @return Images
     */
    public function setLocation($location = null)
    {
        $this->location = $location;

        return $this;
    }

    public function __toString()
    {
        return $this->filename;
    }
}
