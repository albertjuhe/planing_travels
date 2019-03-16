<?php

namespace App\Domain\Travel\DataTransformer;

use App\Domain\Location\Model\Location;

interface LocationsTravelDataTransformer
{
    public function write(Location $location);

    public function read();
}
