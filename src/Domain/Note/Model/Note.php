<?php

namespace App\Domain\Note\Model;

class Note
{
    private $id;

    private $title;

    private $description;

    private $location;

    public function getId()
    {
        return $this->id;
    }

    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setContent(string $content): self
    {
        $this->description = $content;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->description;
    }

    public function setLocation($location = null)
    {
        $this->location = $location;

        return $this;
    }

    public function getLocation()
    {
        return $this->location;
    }
}
