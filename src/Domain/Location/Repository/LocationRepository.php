<?php

namespace App\Domain\Location\Repository;

use App\Domain\Location\Model\Location;

interface LocationRepository
{
    public function save(Location $location);

    public function remove(Location $location);

    public function findById(int $locationId);
}
