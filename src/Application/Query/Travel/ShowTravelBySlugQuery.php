<?php

namespace App\Application\Query\Travel;

use App\Application\Query\Query;

class ShowTravelBySlugQuery implements Query
{
    /** @var string */
    private $slug;

    /**
     * ShowTravelBySlugCommand constructor.
     *
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
}
