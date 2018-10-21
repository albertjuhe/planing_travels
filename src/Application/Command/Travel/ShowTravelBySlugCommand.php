<?php


namespace App\Application\Command\Travel;


class ShowTravelBySlugCommand
{
/** @var string */
    private $slug;

    /**
     * ShowTravelBySlugCommand constructor.
     * @param string $slug
     */
    public function __construct(string $slug)
    {
        $this->slug = $slug;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     */
    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

}