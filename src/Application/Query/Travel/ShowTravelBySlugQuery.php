<?php

namespace App\Application\Query\Travel;

use App\Application\Query\Query;

class ShowTravelBySlugQuery implements Query
{
    private $slug;

    public function __construct(string $slug)
    {
        $this->slug = $slug;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }
}
