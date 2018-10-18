<?php
/**
 * Created by PhpStorm.
 * User: albert.juhe
 * Date: 31/07/2018
 * Time: 08:58
 */

namespace App\Domain\Gpx\Model;

use App\Domain\Travel\Model\Travel;

class Gpx
{
    /** @var int */
    private $id;

    /** @var string */
    private $title;

    /** @var string */
    private $description;

    /** @var string */
    private $filename;

    /** @var string */
    private $color;

    /** @var \DateTime  */
    protected $createdAt;

    /** @var \DateTime  */
    protected $updatedAt;

    /** @var Travel */
    private $travel;

    public function __construct()
    {
        $this->updatedAt = new \DateTime;
        $this->createdAt = new \DateTime;
    }

    /**
     * @param Gpx $gpx
     * @return bool
     */
    public function equals(Gpx $gpx) {
        return $this->id === $gpx->getId();
    }

    /**
     * @return mixed
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * @param mixed $filename
     */
    public function setFilename(string $filename)
    {
        $this->filename = $filename;
    }

    /**
     * @return mixed
     */
    public function getColor(): string
    {
        return $this->color;
    }

    /**
     * @param mixed $color
     */
    public function setColor(string $color)
    {
        $this->color = $color;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return mixed
     */
    public function getTravel(): Travel
    {
        return $this->travel;
    }

    /**
     * @param mixed $travel
     */
    public function setTravel(Travel $travel)
    {
        $this->travel = $travel;
    }



}