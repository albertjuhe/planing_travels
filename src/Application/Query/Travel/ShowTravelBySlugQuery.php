<?php

namespace App\Application\Query\Travel;

use App\Application\Query\Query;

class ShowTravelBySlugQuery implements Query
{
    private $slug;
    private $userId;

    public function __construct(string $slug, ?int $userId = null)
    {
        $this->slug = $slug;
        $this->userId = $userId;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }
}
