<?php

namespace App\Domain\TypeLocation\Repository;

use App\Domain\TypeLocation\Model\TypeLocation;

interface TypeLocationRepository
{
    /**
     * Get all type of locations.
     *
     * @return mixed
     */
    public function getAllTypeLocations();

    public function idOrFail(string $locationType): TypeLocation;
}
