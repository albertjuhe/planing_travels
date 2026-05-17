<?php

namespace App\Application\Query\Weather;

use App\Application\Query\Query;

class GetTravelForecastQuery implements Query
{
    private string $slug;
    private ?int $userId;

    public function __construct(string $slug, ?int $userId)
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
